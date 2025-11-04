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

namespace local_course_checker\privacy;

use context;
use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\writer;

/**
 * Privacy API implementation for local_course_checker.
 *
 * @package    local_course_checker
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\plugin\provider {
    /**
     * Describes the data stored by this plugin.
     *
     * @param collection $collection The metadata collection to add to.
     * @return collection The updated collection.
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table('local_course_checker_event', [
            'userid' => 'privacy:metadata:userid',
            'relateduserid' => 'privacy:metadata:relateduserid',
            'checker_id' => 'privacy:metadata:checker_id',
            'action' => 'privacy:metadata:action',
            'target' => 'privacy:metadata:target',
            'objectid' => 'privacy:metadata:objectid',
            'other' => 'privacy:metadata:other',
            'timecreated' => 'privacy:metadata:timecreated',
        ], 'privacy:metadata:local_course_checker_event');

        return $collection;
    }

    /**
     * Returns the list of contexts that contain user information.
     *
     * @param int $userid The user ID.
     * @return contextlist
     */
    public static function get_contexts_for_userid(int $userid): contextlist {

        $sql = "SELECT DISTINCT ctx.id
                  FROM {context} ctx
                  JOIN {course} c ON c.id = ctx.instanceid
                  JOIN {local_course_checker_event} e ON e.courseid = c.id
                 WHERE e.userid = :userid";
        $params = ['userid' => $userid];

        $contextlist = new contextlist();
        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Export all user data for the approved contexts.
     *
     * @param approved_contextlist $contextlist
     */
    public static function export_user_data(approved_contextlist $contextlist): void {
        global $DB;

        $userid = $contextlist->get_user()->id;

        foreach ($contextlist->get_contexts() as $context) {
            if ($context->contextlevel != CONTEXT_COURSE) {
                continue;
            }

            $courseid = $context->instanceid;

            $events = $DB->get_records('local_course_checker_event', [
                'courseid' => $courseid,
                'userid' => $userid,
            ]);

            if (!empty($events)) {
                writer::with_context($context)->export_data(
                    ['local_course_checker', 'events'],
                    (object) ['events' => array_values($events)]
                );
            }
        }
    }

    /**
     * Delete all data for this user in the provided context.
     *
     * @param approved_contextlist $contextlist
     */
    public static function delete_data_for_user(approved_contextlist $contextlist): void {
        global $DB;

        $userid = $contextlist->get_user()->id;

        foreach ($contextlist->get_contexts() as $context) {
            if ($context->contextlevel != CONTEXT_COURSE) {
                continue;
            }

            $courseid = $context->instanceid;

            $DB->delete_records('local_course_checker_event', [
                'courseid' => $courseid,
                'userid' => $userid,
            ]);
        }
    }

    /**
     * Delete all data for all users in the provided context.
     *
     * @param context $context
     */
    public static function delete_data_for_all_users_in_context(context $context): void {
        global $DB;

        if ($context->contextlevel != CONTEXT_COURSE) {
            return;
        }

        $DB->delete_records('local_course_checker_event', ['courseid' => $context->instanceid]);
    }
}
