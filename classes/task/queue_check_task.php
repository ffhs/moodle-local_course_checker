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
 * Adhoc task to queue the checks.
 *
 * @package    local_course_checker
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_course_checker\task;

use core\task\manager;
use core\task\adhoc_task;
use local_course_checker\db\model\checker;
use stdClass;

/**
 * Adhoc tasks to queue all checks that should run.
 */
class queue_check_task extends adhoc_task {
    /**
     * Create an instance.
     *
     * @param stdClass $user
     * @param stdClass $course
     * @param array $checks
     * @return self
     */
    public static function instance(stdClass $user, stdClass $course, array $checks): self {
        // We don't set_next_run_time, because this adhoc task needs to run directly after the queuing.
        // Lock is also not needed because the task uses the default lock.
        $task = new self();
        // Pass userid to set_userid. This allows to filter via usernames inside the tasklogs.
        $task->set_userid($user->id);
        $data = ['user' => $user, 'course' => $course, 'checks' => $checks];
        $task->set_custom_data($data);
        return $task;
    }

    /**
     * {@inheritDoc}
     */
    public function execute(): void {
        $data = $this->get_custom_data();
        $course = $data->course;
        $user = $data->user;
        $checks = (array)$data->checks;
        mtrace('Queuing course checks.');
        mtrace('Course: ' . $course->fullname . ' (' . $course->id . ').');
        mtrace('User triggering the checks: ' . $user->email);

        $checkcount = count($checks);

        $checker = checker::create_or_update($course->id);

        if ($checkcount > 1) {
            // Delete Checks and Events.
            $checker->remove_checks();
            $checker->remove_events();
        } else {
            // Delete Check.
            $checker->remove_check(array_values($checks)[0]);
        }

        mtrace('Planned checks: ' . $checkcount);
        mtrace("Planned checks:" . implode(", ", $checks));

        foreach ($checks as $checkname) {
            mtrace("Scheduling: " . $checkname);
            $adhoc = run_checker::instance($user, $course, $checker, $checkname);
            manager::queue_adhoc_task($adhoc, true);
            mtrace("---Scheduling done: " . $checkname);
        }

        // Prepare data for notification.
        $sendnotificationadhoc = send_notification::instance($user, $course, $checks);
        manager::queue_adhoc_task($sendnotificationadhoc, true);

        mtrace('Queuing checks completed.');
    }

    /**
     * {@inheritDoc}
     */
    public function get_name(): string {
        return get_string('queue_check_task', 'local_course_checker');
    }

    /**
     * {@inheritDoc}
     */
    public function get_component(): string {
        return 'local_course_checker';
    }
}
