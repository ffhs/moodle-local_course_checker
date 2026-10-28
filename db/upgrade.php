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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin upgrade steps are defined here.
 *
 * @package     local_course_checker
 * @category    upgrade
 * @copyright   2024 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Execute local_course_checker upgrade from the given old version.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_local_course_checker_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();
    if ($oldversion < 2025061000) {
        // Register new capabilities.
        update_capabilities('course_checker');
        upgrade_plugin_savepoint(true, 2025061000, 'local', 'course_checker');
    }
    if ($oldversion < 2026032400) {
        \core\notification::info("local_course_checker changes started. This may take a while.");
        // Get local_course_checker_check table.
        $table = new xmldb_table('local_course_checker_check');

        // Define index checkerid_status_idx (not unique) to be dropped and readded from local_course_checker_check.
        $index = new xmldb_index('checkerid_status_idx', XMLDB_INDEX_NOTUNIQUE, ['checker_id', 'status']);
        // Drop Index.
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Define new default status on table local_course_checker_checker to error.
        $field = new xmldb_field('status', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'error', 'check_name');
        // Launch change of default for field status.
        $dbman->change_field_default($table, $field);

        // Add Index back.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Define field custom_data to be added to local_course_checker_check.
        $field = new xmldb_field('custom_data', XMLDB_TYPE_TEXT, null, null, null, null, null, 'status');
        // Add new filed custom_data.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Get local_course_checker_check_result table.
        $table = new xmldb_table('local_course_checker_check_result');

        // Define index checkid_status_idx (not unique) to be dropped and readded from local_course_checker_check_result.
        $index = new xmldb_index('checkid_status_idx', XMLDB_INDEX_NOTUNIQUE, ['check_id', 'status']);
        // Drop Index.
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Define new default status on table local_course_checker_check_result to error.
        $field = new xmldb_field('status', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'error', 'check_id');
        // Launch change of type for field status.
        $dbman->change_field_type($table, $field);

        // Add Index back.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Define field custom_data to be added to local_course_checker_check_result.
        $field = new xmldb_field('custom_data', XMLDB_TYPE_TEXT, null, null, null, null, null, 'message');
        // Add new filed custom_data.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Course_checker savepoint reached.
        upgrade_plugin_savepoint(true, 2026032400, 'local', 'course_checker');
        \core\notification::success("local_course_checker changes completed successfully.");
    }
    if ($oldversion < 2026051300) {
        \core\notification::info("local_course_checker tables cleanup started. This may take a while.");
        // Delete records that are orphaned due to a bug.
        $sql = "checker_id NOT IN (SELECT id FROM {local_course_checker})";
        $DB->delete_records_select('local_course_checker_check', $sql);
        $DB->delete_records_select('local_course_checker_event', $sql);
        $sql = "check_id NOT IN (SELECT id FROM {local_course_checker_check})";
        $DB->delete_records_select('local_course_checker_check_result', $sql);
        upgrade_plugin_savepoint(true, 2026051300, 'local', 'course_checker');
        \core\notification::success("local_course_checker tables cleanup completed successfully.");
    }

    return true;
}
