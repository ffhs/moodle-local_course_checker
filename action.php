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
 * Parses the form Actions.
 *
 * @package    local_course_checker
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Include Moodle's config file to initialize the Moodle environment.
use core\task\manager;
use local_course_checker\plugininfo\checker;
use local_course_checker\task\queue_check_task;

require_once(__DIR__ . '/../../config.php');

require_login();

// Validate the sesskey (CSRF protection).
require_sesskey(); // Prevents unauthorized requests.

$action = required_param('action', PARAM_ALPHAEXT);
$courseid = required_param('courseid', PARAM_INT);
$returnurl = optional_param('returnurl', new moodle_url('/local/course_checker/index.php', ['courseid' => $courseid]), PARAM_URL);

try {
    // Attempt to retrieve the course.
    $course = get_course($courseid);
    require_login($course); // Ensure the user has access to the course.
} catch (dml_missing_record_exception $e) {
    // Handle the case where the course ID is invalid (does not exist in the database).
    throw new moodle_exception('invalidcourseid', 'error', '', 'Invalid course'); // User-friendly error message.
}

$context = context_course::instance($courseid);

// Retrieve the current user's ID.
$user = $USER;

switch ($action) {
    case 'run_all_checks':
        try {
            require_capability('local/course_checker:runcheck', $context);
        } catch (required_capability_exception $e) {
            // User does not have the required authorization.
            throw new moodle_exception('nopermissions', 'error', '', 'run this check');
        }
        $checks = checker::get_enabled_plugins();
        $adhoc = queue_check_task::instance($user, $course, $checks);
        manager::queue_adhoc_task($adhoc, true);
        break;

    case 'run_check':
        try {
            require_capability('local/course_checker:runcheck', $context);
        } catch (required_capability_exception $e) {
            // User does not have the required authorization.
            throw new moodle_exception('nopermissions', 'error', '', 'run individual check');
        }
        $checkname = required_param('check_name', PARAM_ALPHAEXT);
        $adhoc = queue_check_task::instance($user, $course, [$checkname => $checkname]);
        manager::queue_adhoc_task($adhoc, true);
        break;

    default:
        // Handle invalid or unsupported actions.
        throw new moodle_exception('invalidaction', 'error', '', $action);
}

redirect($returnurl);
