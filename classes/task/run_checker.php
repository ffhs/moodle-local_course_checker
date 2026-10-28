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

/**
 * Adhoc Task to run a check.
 *
 * @package    local_course_checker
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_course_checker\task;

use coding_exception;
use core\task\adhoc_task;
use local_course_checker\db\model\check as checkModel;
use local_course_checker\db\model\checker as checkerModel;
use local_course_checker\model\check_plugin_interface;
use local_course_checker\plugininfo\coursechecker;
use local_course_checker\translation_manager;
use stdClass;
use Throwable;

/**
 * Adhoc task to run a check.
 */
class run_checker extends adhoc_task {
    /**
     * Create an instance.
     *
     * @param stdClass $user
     * @param stdClass $course
     * @param checkerModel $checker
     * @param string $checkname
     * @return self
     * @throws coding_exception
     */
    public static function instance(stdClass $user, stdClass $course, checkerModel $checker, string $checkname): self {
        // We don't set_next_run_time, because this adhoc task needs to run directly after the queuing.
        // Lock is also not needed because the task uses the default lock.
        $task = new self();
        // Pass userid to set_userid. This allows to filter via usernames inside the tasklogs.
        $task->set_userid($user->id);
        $data = ['user' => $user, 'course' => $course, 'checker_id' => $checker->get('id'), 'checkname' => $checkname];
        $task->set_custom_data($data);
        return $task;
    }

    /**
     * {@inheritDoc}
     */
    public function execute() {
        $data = $this->get_custom_data();
        $user = $data->user;
        $course = $data->course;
        $checkerid = $data->checker_id;
        $checkname = $data->checkname;
        $faildelay = $this->get_fail_delay();
        mtrace("Run check: $checkname");
        mtrace("Course: $course->fullname ($course->id)");
        mtrace("User triggering the checks: $user->email");
        mtrace("Checker_id: $checkerid");
        mtrace("Faildelay: $faildelay");

        if (coursechecker::is_plugin_enabled($checkname)) {
            $classnamespace = 'coursechecker_' . $checkname . '\checker';

            if (class_exists($classnamespace)) {
                $checker = new $classnamespace();
                if ($checker instanceof check_plugin_interface) {
                    $check = checkModel::create_or_update($checkerid, $checkname);
                    if ($faildelay > 0) {
                        $check->reset_for_rerun();
                    }
                    try {
                        $checker->run($course, $check);
                    } catch (Throwable $e) {
                        $check->add_error(
                            'Error:',
                            '',
                            translation_manager::generate('error_contact_admin', 'local_course_checker')
                        );
                        throw $e;
                    } finally {
                        $check->update();
                    }
                }
            } else {
                mtrace('Class not found: ' . $classnamespace);
            }
        } else {
            mtrace('Plugin not enabled.');
        }
    }

    /**
     * {@inheritDoc}
     */
    public function get_name(): string {
        return get_string('run_checker_task', 'local_course_checker');
    }

    /**
     * {@inheritDoc}
     */
    public function get_component(): string {
        return 'local_course_checker';
    }
}
