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
 * Parses the form Actions.
 *
 * @package    local_course_checker
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Include Moodle's config file to initialize the Moodle environment.
use core\task\manager;
use local_course_checker\config;
use local_course_checker\db\model\checker as checker_model;
use local_course_checker\output\report_renderer;
use local_course_checker\plugininfo\coursechecker;
use local_course_checker\task\queue_check_task;

require_once(__DIR__ . '/../../config.php');

require_login();

// Validate the sesskey (CSRF protection).
require_sesskey(); // Prevents unauthorized requests.

$action = required_param('action', PARAM_ALPHAEXT);
$courseid = required_param('courseid', PARAM_INT);
$selectedversion = optional_param('selected_version', 'latest', PARAM_TEXT);
$returnurl = optional_param('returnurl', new moodle_url('/local/course_checker/index.php', [
    'courseid' => $courseid,
    'version_name' => $selectedversion,
]), PARAM_URL);

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
        $checks = coursechecker::get_enabled_plugins();
        $adhoc = queue_check_task::instance($user, $course, $checks, $selectedversion);
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
        $adhoc = queue_check_task::instance($user, $course, [$checkname => $checkname], $selectedversion);
        manager::queue_adhoc_task($adhoc, true);
        break;
    case 'delete_all_results':
        if (!config::get(report_renderer::ENABLE_VERSIONING, report_renderer::ENABLE_VERSIONING_DEFAULT)) {
            throw new moodle_exception('versioningdisabled', 'local_course_checker');
        }
        try {
            require_capability('local/course_checker:managereportversions', $context);
        } catch (required_capability_exception $e) {
            throw new moodle_exception('nopermissions', 'error', '', 'delete course check results');
        }

        $checker = checker_model::get_record(['course_id' => $courseid, 'version_name' => $selectedversion]);
        if ($checker) {
            $checker->delete();
        }
        break;
    case 'save_results':
        if (!config::get(report_renderer::ENABLE_VERSIONING, report_renderer::ENABLE_VERSIONING_DEFAULT)) {
            throw new moodle_exception('versioningdisabled', 'local_course_checker');
        }
        try {
            require_capability('local/course_checker:managereportversions', $context);
        } catch (required_capability_exception $e) {
            throw new moodle_exception('nopermissions', 'error', '', 'save course check results');
        }
        $versionname = trim(required_param('version_name', PARAM_TEXT));
        $overwrite = optional_param('overwrite', false, PARAM_BOOL);
        if ($versionname === '') {
            throw new moodle_exception('version_name_required', 'local_course_checker');
        }
        if (core_text::strlen($versionname) > 20) {
            throw new moodle_exception('version_name_too_long', 'local_course_checker');
        }

        // Load all versions once and derive the source, destination, count, and oldest saved version from that result.
        $versions = checker_model::get_records(['course_id' => $courseid]);
        $checker = null;
        $existing = null;
        $oldestsavedversion = null;
        $savedreportcount = 0;
        foreach ($versions as $version) {
            $name = $version->get('version_name');
            if ($name === $selectedversion) {
                $checker = $version;
            }
            if ($name === $versionname) {
                $existing = $version;
            }
            if ($name !== 'latest') {
                $savedreportcount++;
                if ($oldestsavedversion === null || $version->get('timestamp') < $oldestsavedversion->get('timestamp')) {
                    $oldestsavedversion = $version;
                }
            }
        }

        if (!$checker) {
            throw new moodle_exception('invalidrecord', 'error');
        }

        if ($checker->get('version_name') !== $versionname) {
            if ($existing && !$overwrite) {
                throw new moodle_exception('version_name_exists', 'local_course_checker');
            }

            $maxsavedreports = (int) config::get(report_renderer::MAXSAVEDREPORTS, report_renderer::MAXSAVEDREPORTS_DEFAULT);
            $limitreached =
                $selectedversion === 'latest' && !$existing && $maxsavedreports > 0 && $savedreportcount >= $maxsavedreports;
            if ($limitreached && !$overwrite) {
                throw new moodle_exception('saved_report_limit_reached', 'local_course_checker');
            }

            $transaction = $DB->start_delegated_transaction();
            if ($existing) {
                $existing->delete();
            } else if ($limitreached && $oldestsavedversion) {
                $oldestsavedversion->delete();
            }
            $checker->set('version_name', $versionname);
            $checker->update();
            $transaction->allow_commit();
        }

        $returnurl = new moodle_url('/local/course_checker/index.php', [
            'courseid' => $courseid,
            'version_name' => $versionname,
        ]);
        break;
    default:
        // Handle invalid or unsupported actions.
        throw new moodle_exception('invalidaction', 'error', '', $action);
}

redirect($returnurl);
