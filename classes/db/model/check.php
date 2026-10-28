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

namespace local_course_checker\db\model;

use coding_exception;
use core\invalid_persistent_exception;
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
    use has_custom_data;

    /**
     * {@inheritDoc}
     */
    const string TABLE = 'local_course_checker_check';

    /**
     * Creates or updates a check record for a given checker.
     *
     * If a check with the same name already exists for the checker, it updates its status and timestamp.
     * If not, it creates a new record.
     *
     * @param int $checkerid The ID of the checker this check belongs to.
     * @param string $checkname The name of the check.
     * @param string $status The result status (default: 'running').
     * @return self The created or updated check object.
     */
    public static function create_or_update(int $checkerid, string $checkname, string $status = 'running'): self {
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
            'status' => ['type' => PARAM_ALPHANUMEXT, 'default' => 'running'],
            'custom_data' => ['type' => PARAM_TEXT, 'default' => null, 'null' => NULL_ALLOWED],
            'timestamp' => ['type' => PARAM_INT],
        ];
    }

    /**
     * Adds an error result to the check.
     *
     * @param string $title The title of the error.
     * @param string $link A relevant link related to the error.
     * @param string $message The error message.
     * @param array|null $customdata
     * @return check_result
     * @throws coding_exception
     * @throws invalid_persistent_exception
     */
    public function add_error(string $title, string $link, string $message, ?array $customdata = null): check_result {
        $this->set('status', 'error');
        return $this->add_result('error', $title, $link, $message, $customdata);
    }

    /**
     * Adds a result to the check under the given status category.
     *
     * @param string $status The status category ('error', 'failed', 'warning', 'successful', 'info').
     * @param string $title The title of the result.
     * @param string $link A relevant link related to the result.
     * @param string $message The result message.
     * @param array|null $customdata
     * @return check_result
     * @throws invalid_persistent_exception
     * @throws coding_exception
     */
    public function add_result(
        string $status,
        string $title,
        string $link,
        string $message,
        ?array $customdata = null
    ): check_result {
        // Clean Title and Message in case someone tries cross site scripting via them.
        $checkresult = new check_result();
        $checkresult->set('check_id', $this->get('id'));
        $checkresult->set('status', $status);
        $checkresult->set('title', trim(clean_param($title, PARAM_TEXT)));
        $checkresult->set('link', $link);
        $checkresult->set('message', trim(clean_param($message, PARAM_TEXT)));
        $checkresult->set('custom_data', $customdata ?? []);
        $checkresult->set('timestamp', time());
        $checkresult->create();
        return $checkresult;
    }

    /**
     * Adds a failed result to the check.
     *
     * @param string $title The title of the failure.
     * @param string $link A relevant link related to the failure.
     * @param string $message The failure message.
     * @param array|null $customdata
     * @return check_result
     * @throws coding_exception
     * @throws invalid_persistent_exception
     */
    public function add_failed(string $title, string $link, string $message, ?array $customdata = null): check_result {
        $this->set('status', 'failed');
        return $this->add_result('failed', $title, $link, $message, $customdata);
    }

    /**
     * Adds a warning result to the check.
     *
     * @param string $title The title of the warning.
     * @param string $link A relevant link related to the warning.
     * @param string $message The warning message.
     * @param array|null $customdata
     * @return check_result
     * @throws coding_exception
     * @throws invalid_persistent_exception
     */
    public function add_warning(string $title, string $link, string $message, ?array $customdata = null): check_result {
        $this->set('status', 'warning');
        return $this->add_result('warning', $title, $link, $message, $customdata);
    }

    /**
     * Adds a successful result to the check.
     *
     * @param string $title The title of the success.
     * @param string $link A relevant link related to the success.
     * @param string $message The success message.
     * @param array|null $customdata
     * @return check_result
     * @throws coding_exception
     * @throws invalid_persistent_exception
     */
    public function add_successful(string $title, string $link, string $message, ?array $customdata = null): check_result {
        $this->set('status', 'successful');
        return $this->add_result('successful', $title, $link, $message, $customdata);
    }

    /**
     * Adds a info result to the check.
     *
     * @param string $title The title of the info.
     * @param string $link A relevant link related to the info.
     * @param string $message The info message.
     * @param array|null $customdata
     * @return check_result
     * @throws coding_exception
     * @throws invalid_persistent_exception
     */
    public function add_info(string $title, string $link, string $message, ?array $customdata = null): check_result {
        $this->set('status', 'info');
        return $this->add_result('info', $title, $link, $message, $customdata);
    }

    /**
     * Retrieves all results categorized by status.
     *
     * @return array The categorized results.
     */
    public function get_results(): array {
        return check_result::get_records(['check_id' => $this->get('id')]);
    }

    /**
     * Deletes all results of the check and resets the status to 'running'.
     *
     * @return void
     */
    public function reset_for_rerun(): void {
        $this->raw_set('status', 'running');
        $this->set('timestamp', time());
        $this->delete_results();
        $this->update();
    }

    /**
     * Deletes all results of the check.
     *
     * @return array The categorized results.
     */
    public function delete_results(): void {
        global $DB;
        $DB->delete_records(check_result::TABLE, ['check_id' => $this->get('id')]);
        mtrace("Deleted all check_result(s) for check ID: " . $this->get('id'));
    }

    /**
     * Validates the status value before it's set.
     *
     * Prevents the check status from being downgraded if it's already set to 'error'.
     *
     * @param string $value The new status value.
     * @return void
     * @throws coding_exception
     */
    protected function set_status(string $value): void {
        $current = (string) $this->raw_get('status');
        $value = (string) $value;

        $allowed = [
            'running' => ['info', 'warning', 'failed', 'successful', 'error'],
            'successful' => ['info', 'warning', 'failed', 'error'],
            'info' => ['warning', 'failed', 'error'],
            'warning' => ['failed', 'error'],
            'failed' => ['error'],
            'error' => [],
        ];

        // Allow setting the same status again without triggering transition validation.
        if ($value === $current) {
            return;
        }

        if (!isset($allowed[$current]) || !in_array($value, $allowed[$current], true)) {
            return;
        }

        $this->raw_set('status', $value);
    }

    /**
     * {@inheritDoc}
     */
    protected function before_delete(): void {
        $this->delete_results();
    }

    /**
     * {@inheritDoc}
     */
    protected function before_update(): void {
        if ($this->raw_get('status') === 'running') {
            // Set Status to successful if the status is still running and the check is done.
            $this->set('status', 'successful');
        }
    }
}
