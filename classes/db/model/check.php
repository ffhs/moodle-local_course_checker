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

use coding_exception;
use core\persistent;

/**
 * Persistent model representing an individual check result for a checker run.
 *
 * Each check is associated with a specific checker and holds the current status,
 * name, and timestamp. Prevents status from being downgraded once set to 'error'.
 *
 * @package    local_course_checker
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class check extends persistent {
    /**
     * {@inheritDoc}
     */
    const TABLE = 'local_course_checker_check';

    /**
     * Validates the status value before it's set.
     *
     * Prevents the check status from being downgraded if it's already set to 'error'.
     *
     * @param string $value The new status value.
     * @return string|bool Returns true if valid, or false to reject.
     */
    protected function validate_status(string $value): string|bool {
        // Prevent status downgrade from 'error'.
        if ($this->raw_get('status') === 'error' && $value !== 'error') {
            debugging("Status is already 'error'; can't be downgraded to '{$value}'", DEBUG_DEVELOPER);
            return false; // Reject the change.
        }
        return true;
    }

    /**
     * Creates or updates a check record for a given checker.
     *
     * If a check with the same name already exists for the checker, it updates its status and timestamp.
     * If not, it creates a new record.
     *
     * @param int $checkerid The ID of the checker this check belongs to.
     * @param string $checkname The name of the check.
     * @param string $status The result status (default: 'successful').
     * @return self The created or updated check object.
     */
    public static function create_or_update(int $checkerid, string $checkname, string $status = 'successful'): self {
        $existing = self::get_record([
            'checker_id' => $checkerid,
            'check_name' => $checkname,
        ]);

        if ($existing) {
            $existing->set('status', $status);
            $existing->set('timestamp', time());
            $existing->update();
            mtrace("Check '$checkname' updated for checker ID $checkerid.");
            return $existing;
        }

        $check = new self();
        $check->set('checker_id', $checkerid);
        $check->set('check_name', $checkname);
        $check->set('status', $status);
        $check->set('timestamp', time());
        $check->create();
        mtrace("Check '$checkname' created for checker ID $checkerid.");
        return $check;
    }

    /**
     * Deletes check results for a given checker ID.
     *
     * @param int $checkerid
     * @param string|null $checkname
     * @return bool
     * @throws coding_exception
     */
    public static function delete_by_id(int $checkerid, ?string $checkname = null): bool {
        $conditions = ['checker_id' => $checkerid];

        if (!is_null($checkname)) {
            $conditions['check_name'] = $checkname;
        }

        $checks = self::get_records($conditions);

        foreach ($checks as $check) {
            $check->delete(); // Triggers persistent deletion logic.
        }

        if (is_null($checkname)) {
            mtrace("All check deleted for checker_id: $checkerid");
        } else {
            mtrace("Check for check_name: $checkname with checker_id: $checkerid");
        }
        return true;
    }

    /**
     * Deletes check results by check name.
     *
     * @param string $checkname
     * @return void
     * @throws coding_exception
     */
    public static function delete_by_check_name(string $checkname): void {
        $checks = self::get_records(['check_name' => $checkname]);

        foreach ($checks as $check) {
            $check->delete();
        }

        mtrace("Old result(s) deleted for check_name: $checkname");
    }

    /**
     * {@inheritDoc}
     */
    protected static function define_properties() {
        return [
            'checker_id' => ['type' => PARAM_INT],
            'check_name' => ['type' => PARAM_TEXT],
            'status' => ['type' => PARAM_ALPHANUMEXT, 'default' => 'successful'],
            'timestamp' => ['type' => PARAM_INT],
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function before_delete(): void {
        global $DB;
        $DB->delete_records(check_result::TABLE, ['check_id' => $this->get('id')]);
        mtrace("Deleted all check_result(s) for check ID: " . $this->get('id'));
    }

    /**
     * {@inheritDoc}
     */
    protected function before_update(): void {
        // Don't allow status downgrade if it's already "error".
        if ($this->raw_get('status') === 'error' && $this->get('status') !== 'error') {
            // Revert the status change silently.
            $this->set('status', 'error');
            mtrace("Attempted to downgrade status from 'error'. Reverted back.");
        }
    }

    /**
     * Adds an error result to the check.
     *
     * @param string $title The title of the error.
     * @param string $link A relevant link related to the error.
     * @param string $message The error message.
     */
    public function add_error(string $title, string $link, string $message): check_result {
        $this->set('status', 'error');
        return $this->add_result('error', $title, $link, $message);
    }

    /**
     * Adds a failed result to the check.
     *
     * @param string $title The title of the failure.
     * @param string $link A relevant link related to the failure.
     * @param string $message The failure message.
     */
    public function add_failed(string $title, string $link, string $message): check_result {
        return $this->add_result('failed', $title, $link, $message);
    }

    /**
     * Adds a warning result to the check.
     *
     * @param string $title The title of the warning.
     * @param string $link A relevant link related to the warning.
     * @param string $message The warning message.
     */
    public function add_warning(string $title, string $link, string $message): check_result {
        return $this->add_result('warning', $title, $link, $message);
    }

    /**
     * Adds a successful result to the check.
     *
     * @param string $title The title of the success.
     * @param string $link A relevant link related to the success.
     * @param string $message The success message.
     */
    public function add_successful(string $title, string $link, string $message): check_result {
        return $this->add_result('successful', $title, $link, $message);
    }

    /**
     * Adds a result to the check under the given status category.
     *
     * @param string $status The status category ('error', 'failed', 'warning', 'successful').
     * @param string $title The title of the result.
     * @param string $link A relevant link related to the result.
     * @param string $message The result message.
     */
    public function add_result(string $status, string $title, string $link, string $message): check_result {
        $checkresult = new check_result();
        $checkresult->set('check_id', $this->get('id'));
        $checkresult->set('status', $status);
        $checkresult->set('title', $title);
        $checkresult->set('link', $link);
        $checkresult->set('message', $message);
        $checkresult->set('timestamp', time());
        $checkresult->create();
        return $checkresult;
    }

    /**
     * Retrieves all results categorized by status.
     *
     * @return array The categorized results.
     */
    public function get_results(): array {
        return check_result::get_records(['check_id' => $this->get('id')]);
    }
}
