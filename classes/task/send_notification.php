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

/**
 * Adhoc Task to send a notification after the checks are done.
 *
 * @package    local_course_checker
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_course_checker\task;

use core\task\adhoc_task;
use core\task\manager;
use local_course_checker\db\database_manager as dbm;
use local_course_checker\notification;
use stdClass;

/**
 * Adhoc tasks to notify the user after all tasks are done.
 */
class send_notification extends adhoc_task {
    /**
     * Create an instance and set next runtime.
     *
     * @param stdClass $user
     * @param stdClass $course
     * @param array|stdClass $checks
     * @return self
     */
    public static function instance(stdClass $user, stdClass $course, array|stdClass $checks): self {
        // Lock is not needed because the task uses the default lock.
        $task = new self();
        // Pass userid to set_userid. This allows to filter via usernames inside the tasklogs.
        $task->set_userid($user->id);
        $data = ['user' => $user, 'course' => $course, 'checks' => $checks];
        $task->set_custom_data($data);
        // The task will run 5 sec from instancing.
        $task->set_next_run_time(time() + 5);
        $task->set_attempts_available(24);
        return $task;
    }

    /**
     * {@inheritDoc}
     */
    public function execute(): void {
        $data = $this->get_custom_data();
        $course = $data->course;
        $user = $data->user;
        $checks = $data->checks;
        $queuecheckclass = '\\' . queue_check_task::class;
        $runcheckerclass = '\\' . run_checker::class;

        $attempts = $this->get_attempts_available();
        mtrace("Attempts: " . $attempts);

        // Check if queuing is done.

        if (dbm::planned_adhoc_tasks($queuecheckclass, $course->id) || dbm::planned_adhoc_tasks($runcheckerclass, $course->id)) {
            if ($attempts > 1) {
                $sendnotificationadhoc = self::instance($user, $course, $checks);
                $sendnotificationadhoc->set_attempts_available($attempts - 1);
                mtrace('Running tasks detected. Reschedule task');
                manager::queue_adhoc_task($sendnotificationadhoc);
            } else {
                notification::failed($user, $course);
            }
        } else {
            mtrace('No running task detected. Send notification.');
            notification::successful($user, $course, $checks);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function get_component(): string {
        return 'local_course_checker';
    }

    /**
     * {@inheritDoc}
     */
    public function get_name(): string {
            return get_string('send_notification_task', 'local_course_checker');
    }
}
