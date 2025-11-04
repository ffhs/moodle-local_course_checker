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

namespace local_course_checker\db\model;

use core\persistent;
use dml_exception;

/**
 * Persistent model representing a course checker run.
 *
 * Each checker is tied to a specific course and version, and stores the timestamp
 * of when it was run. Associated checks and events are stored in related tables.
 *
 * @package    local_course_checker
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class checker extends persistent {
    /**
     * {@inheritDoc}
     */
    const string TABLE = 'local_course_checker';

    /**
     * {@inheritDoc}
     */
    protected static function define_properties() {
        return [
            'version_name' => ['type' => PARAM_ALPHANUMEXT, 'default' => 'latest'],
            'course_id' => ['type' => PARAM_INT],
            'timestamp' => ['type' => PARAM_INT],
        ];
    }


    /**
     * Creates a new checker or updates the timestamp of an existing one for a course.
     *
     * @param int $courseid The course ID.
     * @param string $versionname The checker version name (default: 'latest').
     * @return self The newly created or updated checker instance.
     */
    public static function create_or_update(int $courseid, string $versionname = 'latest'): self {
        $existing = self::get_record([
            'course_id' => $courseid,
            'version_name' => $versionname,
        ]);

        if ($existing) {
            $existing->set('timestamp', time());
            $existing->update();
            mtrace("Checker found and updated.");
            return $existing;
        }

        $checker = new self();
        $checker->set('course_id', $courseid);
        $checker->set('version_name', $versionname);
        $checker->set('timestamp', time());
        $checker->create();
        mtrace("Checker created and inserted.");
        return $checker;
    }

    /**
     * Cleanup handler triggered before deleting this checker.
     *
     * Removes all related checks, check results, and logged events to prevent orphaned data.
     *
     * @return void
     * @throws dml_exception
     */
    public function before_delete(): void {
        global $DB;

        $checkerid = $this->get('id');

        // 1. Delete all check_results.
        $checkids = array_keys(check::get_records(['checker_id' => $checkerid]));
        if (!empty($checkids)) {
            [$insql, $inparams] = $DB->get_in_or_equal($checkids);
            $DB->delete_records_select(check_result::TABLE, "check_id $insql", $inparams);
            mtrace("Deleted check_results for checks of checker ID: $checkerid");
        }

        // 2. Delete all checks.
        $DB->delete_records(check::TABLE, ['checker_id' => $checkerid]);
        mtrace("Deleted checks for checker ID: $checkerid");

        // 3. Delete all related events.
        $DB->delete_records(event::TABLE, ['checker_id' => $checkerid]);
        mtrace("Deleted events for checker ID: $checkerid");
    }

    /**
     * Deletes a specific check by name from this checker.
     *
     * @param string $checkname The check name to remove.
     * @return void
     * @throws dml_exception
     */
    public function remove_check(string $checkname): void {
        global $DB;
        $checkerid = $this->get('id');

        $DB->delete_records(check::TABLE, ['checker_id' => $checkerid, 'check_name' => $checkname]);
        mtrace("Deleted checks for checker ID: $checkerid and checkname: $checkname");
    }

    /**
     * Deletes all checks associated with this checker.
     *
     * @return void
     * @throws dml_exception
     */
    public function remove_checks(): void {
        global $DB;
        $checkerid = $this->get('id');

        $DB->delete_records(check::TABLE, ['checker_id' => $checkerid]);
        mtrace("Deleted checks for checker ID: $checkerid");
    }

    /**
     * Deletes all events associated with this checker.
     *
     * @return void
     * @throws dml_exception
     */
    public function remove_events(): void {
        global $DB;
        $checkerid = $this->get('id');

        $DB->delete_records(event::TABLE, ['checker_id' => $checkerid]);
        mtrace("Deleted events for checker ID: $checkerid");
    }

    /**
     * Retrieves all check records linked to this checker.
     *
     * @return array An array of check persistent objects.
     */
    public function get_checks(): array {
        return check::get_records(['checker_id' => $this->get('id')]);
    }

    /**
     * Retrieves all event records linked to this checker.
     *
     * @return array An array of event persistent objects.
     */
    public function get_events(): array {
        return event::get_records(['checker_id' => $this->get('id')]);
    }
}
