<?php
// This file is part of Moodle - https://moodle.org/
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

use core\output\choicelist;
use core\output\local\dropdown\status as status_dropdown;
use local_course_checker\config;
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
 * Renderer class responsible for outputting report visual components of the course checker.
 *
 * This includes results, headers, and event logs. Uses Moodle's Mustache templating engine.
 *
 * @package    local_course_checker
 * @category   output
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @copyright  2025 Simon Gisler, Fernfachhochschule Schweiz (FFHS) <simon.gisler@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_renderer extends plugin_renderer_base {
    /** Settings path for the enable versioning setting. */
    const string ENABLE_VERSIONING = 'local_course_checker/enableversioning';

    /** Settings default for the enable versioning setting. */
    const bool ENABLE_VERSIONING_DEFAULT = true;

    /** Settings path for the maximal saveable reports setting. */
    const string MAXSAVEDREPORTS = 'local_course_checker/maxsavedreports';

    /** Settings default for the maximal of reports that can be saved. */
    const int MAXSAVEDREPORTS_DEFAULT = 3;

    /** @var stdClass The course object this renderer is tied to. */
    private stdClass $course;

    /** @var string The selected checker version. */
    private string $versionname = 'latest';

    /** @var checker_model|bool The current checker model instance or false if not found. */
    private checker_model|bool $checker;

    /** @var bool Is one check or more queued for a run. */
    private bool $queueinprogress = false;

    /** @var bool Is one check or more running. */
    private bool $runinprogress = false;

    /** @var array Array of queued check tasks. */
    private array $queuechecktasks;

    /**
     * Initializes the renderer with course context and loads the latest checker model.
     *
     * @param stdClass $course The course object.
     * @param string $versionname The version name to load. Defaults to 'latest'.
     * @return checker_model|bool The checker model or false if none found.
     * @throws \dml_exception
     */
    public function init(stdClass $course, string $versionname = 'latest'): bool|checker_model {
        $this->checker = checker_model::get_record(['course_id' => $course->id, 'version_name' => $versionname]);
        if (!$this->checker && $versionname !== 'latest') {
            $versionname = 'latest';
            $this->checker = checker_model::get_record(['course_id' => $course->id, 'version_name' => $versionname]);
        }
        $this->course = $course;
        $this->versionname = $versionname;

        if ($this->queuechecktasks = dbm::planned_adhoc_tasks('\\' . queue_check_task::class, $this->course->id)) {
            if (array_filter($this->queuechecktasks, fn($item) => preg_match('/"checks":{\s*.*/', $item->customdata))) {
                $this->queueinprogress = true;
            }
        }

        if ($runningadhoc = dbm::planned_adhoc_tasks('\\' . run_checker::class, $this->course->id)) {
            if (array_filter($runningadhoc, fn($item) => $item->faildelay == 0)) {
                $this->runinprogress = true;
            }
        }
        return $this->checker;
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
                    $customcheckresultoutput = 'coursechecker_' . $check->get('check_name') . '\output\check_result';
                    // Keep the slash of $customcheckresultmustache like this, else it won't work.
                    $customcheckresultmustache = 'coursechecker_' . $check->get('check_name') . '/check_result';
                    if (class_exists($customcheckresultoutput)) {
                        $output = new $customcheckresultoutput($checkresult);
                    } else {
                        $output = new check_result_output($checkresult);
                    }
                    try {
                        $renderedresults[$checkresult->get('status')][] =
                            $this->render_from_template($customcheckresultmustache, $output->export_for_template($this));
                    } catch (\moodle_exception $e) {
                        $renderedresults[$checkresult->get('status')][] =
                            $this->render_from_template("local_course_checker/check_result", $output->export_for_template($this));
                    }
                }

                // Check if queuing is done.
                $inprogress = false;
                if ($this->queuechecktasks) {
                    foreach ($this->queuechecktasks as $task) {
                        if (isset($task->customdata)) {
                            if (!is_array($task->customdata)) {
                                $task->customdata = json_decode($task->customdata, true, 512, JSON_THROW_ON_ERROR);
                            }
                            if (
                                isset($task->customdata['checks']) &&
                                array_key_exists($check->get('check_name'), $task->customdata['checks'])
                            ) {
                                $inprogress = true;
                            }
                        }
                    }
                }

                $customcheckoutput = 'coursechecker_' . $check->get('check_name') . '\output\check';
                // Keep the slash of $customcheckmustache like this, else it won't work.
                $customcheckmustache = 'coursechecker_' . $check->get('check_name') . '/check';
                if (class_exists($customcheckoutput)) {
                    $output = new $customcheckoutput($check, $renderedresults, $this->course->id, $inprogress);
                } else {
                    $output = new check_output($check, $renderedresults, $this->course->id, $inprogress, $this->versionname);
                }
                try {
                    $renderedcheck = $this->render_from_template($customcheckmustache, $output->export_for_template($this));
                } catch (\moodle_exception $e) {
                    $renderedcheck = $this->render_from_template("local_course_checker/check", $output->export_for_template($this));
                }

                $status = $check->get('status');
                if (!isset($renderedchecks[$status])) {
                    $renderedchecks[$status] = [
                        'checks' => [],
                        'helpicon' => $this->output->help_icon($status . '_checks', 'local_course_checker'),
                    ];
                }
                if (empty($checkresults)) {
                    $renderedchecks[$status]['checks'][] = $renderedcheck;
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
        $data->version_name = $this->versionname;

        if ($this->checker) {
            $data->timestamp = userdate($this->checker->get('timestamp'), '%A, %x %R');
        }

        if ($this->runinprogress || $this->queueinprogress) {
            $data->in_progress = true;
        }

        return $this->render_from_template('local_course_checker/header_bar', $data);
    }

    /**
     * Renders the version control bar with a dropdown to select a version.
     *
     * @return bool|string
     * @throws \coding_exception
     * @throws \core\exception\moodle_exception
     * @throws \dml_exception
     */
    public function render_versioning_bar(): bool|string {
        $enableversioning = config::get(self::ENABLE_VERSIONING, self::ENABLE_VERSIONING_DEFAULT);
        if (!$enableversioning || (!$versions = checker_model::get_records(['course_id' => $this->course->id]))) {
            return '';
        }

        $data = new stdClass();
        $versionchoices = new choicelist();
        $versionchoices->add_option('latest', get_string('checker_version_latest', 'local_course_checker'), [
            'url' => new moodle_url('/local/course_checker/index.php', [
                'courseid' => $this->course->id,
                'version_name' => 'latest',
            ]),
        ]);
        $oldestsavedversion = null;
        $savedreportcount = 0;
        foreach ($versions as $version) {
            $name = $version->get('version_name');
            $data->saved_versions[] = ['name' => $name];
            if ($name !== 'latest') {
                $savedreportcount++;
                if ($oldestsavedversion === null || $version->get('timestamp') < $oldestsavedversion->get('timestamp')) {
                    $oldestsavedversion = $version;
                }
                $versionchoices->add_option($name, $name, [
                    'url' => new moodle_url('/local/course_checker/index.php', [
                        'courseid' => $this->course->id,
                        'version_name' => $name,
                    ]),
                ]);
            }
        }
        $versionchoices->set_selected_value($this->versionname);
        $versiondropdown = new status_dropdown(
            $versionchoices->get_selected_content($this),
            $versionchoices,
            [
                'buttonclasses' => 'btn btn-outline-secondary',
                'extras' => ['data-region' => 'course-checker-version-dropdown'],
            ],
        );
        $data->version_dropdown = $this->render($versiondropdown);

        if ($this->runinprogress || $this->queueinprogress || !$this->checker) {
            $data->locked = true;
        } else {
            $data->action_url = new moodle_url('/local/course_checker/action.php'); // The URL of your action file.
            $data->courseid = $this->course->id; // The current course ID.
            $data->sesskey = sesskey(); // Moodle's CSRF protection token.
            $data->version_name = $this->versionname;
            $data->default_version_name = $this->versionname;
            if ($this->versionname === 'latest') {
                $data->default_version_name = userdate(time(), '%Y_%m_%d');
            }
            $data->returnurl = new moodle_url('/local/course_checker/index.php', [
                'courseid' => $this->course->id,
                'version_name' => $this->versionname,
            ]);
            $data->max_saved_reports = config::get(self::MAXSAVEDREPORTS, self::MAXSAVEDREPORTS_DEFAULT);
            $data->saved_report_count = $savedreportcount;
            $data->oldest_saved_version = $oldestsavedversion?->get('version_name') ?? '';
        }
        return $this->render_from_template('local_course_checker/versioning_bar', $data);
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
        }
        $latest = 0;
        $data['eventslog'] = [];
        $data['helpicon'] = $this->output->help_icon('activity', 'local_course_checker');
        foreach ($events as $event) {
            $latest = max($latest, $event->get('timecreated'));
            $output = new event_output($event);
            array_unshift($data['eventslog'], $output->export_for_template($this));
        }
        $data['timestamp'] = userdate($latest, '%A, %x %R');
        return ($this->render_from_template('local_course_checker/events', $data));
    }
}
