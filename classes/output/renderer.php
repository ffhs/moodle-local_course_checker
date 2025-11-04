<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace local_course_checker\output;

use local_course_checker\db\database_manager as dbm;
use local_course_checker\db\model\check as check_model;
use local_course_checker\db\model\check_result as check_result_model;
use local_course_checker\db\model\checker as checker_model;
use local_course_checker\db\model\event as event_model;
use local_course_checker\output\check as check_output;
use local_course_checker\output\check_result as check_result_output;
use local_course_checker\output\checker as checker_output;
use local_course_checker\output\event as event_output;
use local_course_checker\task\queue_check_task;
use local_course_checker\task\run_checker;
use moodle_exception;
use moodle_url;
use plugin_renderer_base;
use stdClass;

/**
 * Renderer class responsible for outputting all visual components of the course checker.
 *
 * This includes results, headers, and event logs. Uses Moodle's Mustache templating engine.
 *
 * @package    local_course_checker
 * @category   output
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @copyright  2025 Simon Gisler, Fernfachhochschule Schweiz (FFHS) <simon.gisler@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends plugin_renderer_base {
    /** @var stdClass The course object this renderer is tied to. */
    private stdClass $course;

    /** @var checker_model|bool The current checker model instance or false if not found. */
    private checker_model|bool $checker;

    /**
     * Initializes the renderer with course context and loads the latest checker model.
     *
     * @param stdClass $course The course object.
     * @return checker_model|bool The checker model or false if none found.
     */
    public function init(stdClass $course) {
        $this->course = $course;
        return $this->checker = checker_model::get_record(['course_id' => $course->id, 'version_name' => 'latest']);
    }

    /**
     * Renders the full course checker results grouped by status (error, failed, etc.).
     *
     * @return string The rendered HTML output for all checker results.
     */
    public function render_result(): string {
        $checks = check_model::get_records(['checker_id' => $this->checker->get('id')]);
        if (empty($checks)) {
            return "";
        } else {
            $renderedchecks = [];
            foreach ($checks as $check) {
                $checkresults = check_result_model::get_records(['check_id' => $check->get('id')]);
                $renderedresults = [];
                foreach ($checkresults as $checkresult) {
                    $output = new check_result_output($checkresult);
                    $renderedresults[$checkresult->get('status')][] = $this->render_from_template("local_course_checker/check_result", $output->export_for_template($this));
                }

                $inprogress = false;
                if ($runningadhoc = dbm::planned_adhoc_tasks('\\' . queue_check_task::class, $this->course->id)) {
                    if (
                        array_filter($runningadhoc, fn($item) =>
                        preg_match('/"checks"\s*:\s*{[^}]*"' . preg_quote($check->get('check_name'), '/') . '"\s*:/', $item->customdata))
                    ) {
                        $inprogress = true;
                    }
                }

                $output = new check_output($check, $renderedresults, $this->course->id, $inprogress);
                $renderedcheck = $this->render_from_template("local_course_checker/check", $output->export_for_template($this));
                $status = $check->get('status');
                if (!isset($renderedchecks[$status])) {
                    $renderedchecks[$status] = ['checks' => []];
                }
                if (empty($checkresults)) {
                    array_push($renderedchecks[$status]['checks'], $renderedcheck);
                } else {
                    array_unshift($renderedchecks[$status]['checks'], $renderedcheck);
                }
            }
            $output = new checker_output($this->checker, $renderedchecks);
            return ($this->render_from_template("local_course_checker/checker", $output->export_for_template($this)));
        }
    }

    /**
     * Renders the header bar showing the last run timestamp and a "run all checks" button.
     *
     * @return bool|string Rendered header HTML or false on failure.
     */
    public function render_header(): bool|string {
        $data = new stdClass();

        $data->action_url = new moodle_url('/local/course_checker/action.php'); // The URL of your action file.
        $data->courseid = $this->course->id; // The current course ID.
        $data->sesskey = sesskey(); // Moodle's CSRF protection token.

        if ($this->checker) {
            $data->timestamp = userdate($this->checker->get('timestamp'), '%A, %x %R');
        }

        if ($runningadhoc = dbm::planned_adhoc_tasks('\\' . queue_check_task::class, $this->course->id)) {
            if (array_filter($runningadhoc, fn($item) => preg_match('/"checks":{\s*.*/', $item->customdata))) {
                $data->in_progress = true;
            }
        }

        if ($runningadhoc = dbm::planned_adhoc_tasks('\\' . run_checker::class, $this->course->id)) {
            if (array_filter($runningadhoc, fn($item) => $item->faildelay == 0)) {
                $data->in_progress = true;
            }
        }

        return $this->render_from_template('local_course_checker/header_bar', $data);
    }

    /**
     * Renders the activity log of recent events/changes in the course.
     *
     * @return bool|string Rendered HTML of the activity log or an empty string if no events exist.
     * @throws moodle_exception If rendering or DB operations fail.
     */
    public function render_activity(): bool|string {
        $events = event_model::get_records(['checker_id' => $this->checker->get('id')]);
        if (empty($events)) {
            return "";
        } else {
            $latest = 0;
            $data['eventslog'] = [];
            foreach ($events as $event) {
                $latest = max($latest, $event->get('timecreated'));
                $output = new event_output($event);
                array_unshift($data['eventslog'], $output->export_for_template($this));
            }
            $data['timestamp'] = userdate($latest, '%A, %x %R');
        }
        return ($this->render_from_template('local_course_checker/events', $data));
    }
}
