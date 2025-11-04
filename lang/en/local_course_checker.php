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
 * Plugin strings are defined here.
 *
 * @package     local_course_checker
 * @category    string
 * @copyright   2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Course checker';
$string['subplugintype_checker'] = 'Course checker';
$string['subplugintype_checker_plural'] = 'Course checkers';
$string['settings_name'] = 'Course checker general settings';
$string['plugin_enabled'] = '{$a} enabled.';
$string['plugin_disabled'] = '{$a} disabled.';
$string['settings_general'] = '<p>Reload the page if you enable or disable a plugin.</p>';
$string['settings_referencecourseid'] = 'Reference course id';
$string['course_checker:view'] = 'View the course checker';
$string['course_checker:runcheck'] = 'Can run the course checker';
$string['course_checker:view_navigation'] = 'Show Course Checker in Navigation Menu';

// Notification.
$string['course_checker:view_notification'] = 'View the course checker notifications';
$string['messageprovider:checker_completed'] = 'Course check is completed';
// String for messageprovider.
// Multiple checks.
$string['messageprovider_subject'] = 'Checks completed on course {$a}';
$string['messageprovider_completed'] = 'The checks are completed.';
// Only one checks.
$string['messageprovider_singlechecks_subject'] = 'Check {$a->checkername} completed on course {$a->coursename}';
$string['messageprovider_singlechecks_completed'] = 'The check {$a->checkername} is completed.';
$string['messageprovider_result_label'] = 'Results';
$string['messageprovider_greeting'] = 'Hello';
$string['messageprovider_following_checks_completed'] = 'Following checks were completed:';
// Fail in check tasks.
$string['messageprovider:checker_failed'] = 'Course check failed';
$string['messageprovider_subject_failed'] = 'An error occurred during the course check.';
$string['messageprovider_failed_notification_html'] = 'Hello {$a->firstname},<br><br>
Unfortunately, the course checker for the course <strong>{$a->coursename}</strong> could not be completed.<br><br>
Please contact the administrator.';
$string['messageprovider_failed_notification_small'] = 'Course check could not be completed.';

// Result page.
$string['checker_last_run'] = 'Last run: {$a}';
$string['checker_never_run'] = 'Never';
$string['failed_checks'] = 'Failed';
$string['error_checks'] = 'Error';
$string['warning_checks'] = 'Warning';
$string['successful_checks'] = 'Successful';
$string['check_course'] = 'Check this course';
$string['check_course_in_progress'] = 'Check in progress...';
$string['changes_last_check'] = 'Changes since last check: {$a}';
$string['save_results'] = 'Save results'; // ToDo: this is only the translation, saving the result is not implemented yet.

// Event log translations.
$string['activity'] = 'Activity';
$string['last_modified_activity'] = 'Last modified activities since last check';
$string['action'] = 'Action';
$string['course'] = 'Course';
$string['course_section'] = 'Section';
$string['user'] = 'User';
$string['timestamp'] = 'Timestamp';
$string['details'] = 'Details';
$string['created'] = 'Created';
$string['updated'] = 'Updated';
$string['deleted'] = 'Deleted';
$string['unknown'] = 'Unknown';

// Custom Admin Settings.
$string['admin_setting_regex_invalidregex'] = 'The regular expression provided is invalid. Please check your syntax.';

$string['admin_setting_coursesregex'] = 'Course fullname regex filter';
$string['admin_setting_coursesregex_help'] =
        'Define the regex to allow this checker only where it matches the course fullnames.';
$string['admin_setting_coursesregex_skip_course'] = 'Check skipped this course';
$string['admin_setting_coursesregex_skip_course_desc'] = 'Regular expression didn\'t match the course fullname.';

$string['admin_setting_restrictedint_min'] = 'Minimum value is {$a}';
$string['admin_setting_restrictedint_max'] = 'Maximum value is {$a}';

// String specific for the resolution link helper.
$string['checker_links_activity'] = 'Activity: {$a->name} ({$a->modname})';
$string['groups_activity'] = 'Activity "{$a->name}"';

// Tasks.
$string['queue_check_task'] = 'Queue check';
$string['run_checker_task'] = 'Run check';
$string['send_notification_task'] = 'Send notification';

// Privacy provider.
$string['privacy:metadata:local_course_checker_event'] = 'Stores events and activity logs from the course checker.';
$string['privacy:metadata:userid'] = 'The ID of the user who triggered the event.';
$string['privacy:metadata:relateduserid'] = 'Related user affected by the event.';
$string['privacy:metadata:checker_id'] = 'The ID of the checker run.';
$string['privacy:metadata:action'] = 'The action taken (e.g., created, updated, deleted).';
$string['privacy:metadata:target'] = 'The target object (e.g., course_module).';
$string['privacy:metadata:objectid'] = 'The ID of the affected object.';
$string['privacy:metadata:other'] = 'Additional event data in JSON format.';
$string['privacy:metadata:timecreated'] = 'The time the event was created.';
