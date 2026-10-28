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
 * Strings for component 'coursechecker_userdata'.
 *
 * @package    coursechecker_userdata
 * @copyright  2025 Simon Gisler, Fernfachhochschule Schweiz (FFHS) <simon.gisler@ffhs.ch>
 * @copyright  based on work by 2019 Liip SA <elearning@liip.ch>
 * @copyright  based on work by 2019 Adrian Perez, Fernfachhochschule Schweiz (FFHS) <adrian.perez@ffhs.ch>
 * @copyright  based on work by 2020 Christoph Karlen, Fernfachhochschule Schweiz (FFHS) <christoph.karlen@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'User data check';
$string['pluginname_help'] = 'This check searches visible, viewable activities of the configured supported types for existing database, glossary, wiki, or forum content. It runs only for courses matching the configured course pattern; unsupported or disabled types are ignored, and activities whose names match the configured regular-expression whitelist are skipped.';
$string['privacy:metadata'] = 'This check does not store any personal data. Its results are stored by the Course Checker plugin.';

// String specific for the userdata checker.
$string['userdata_setting_modules'] = 'Enabled modules';
$string['userdata_setting_modules_help'] = 'Define the allowed modules (must be enabled in <a href="{$a}" target="_blank">Manage activities</a>, contain reset_userdata method in <code>mod/{modname}/lib.php</code> and supported by this plugin) to be checked for user data.';
$string['userdata_error'] = 'There shouldn\'t be any user data in the {$a} activity.';
$string['userdata_success'] = 'The {$a} activity contains no user data.';
$string['userdata_skip'] = 'The activity "{$a}" was skipped because it is on the whitelist.';
$string['userdata_help'] = 'If you want this data to be copied to other courses, you have to import it manually. Here are some useful manuals: <a href="https://docs.moodle.org/38/en/Backup_of_user_data" target="_blank">Backup of user data</a> and <a href="https://docs.moodle.org/38/en/Reusing_activities" target="_blank">Reusing activities</a>.';
$string['regex_forum_whitelist'] = 'Forum whitelist';
$string['regex_forum_whitelist_desc'] = 'Forums whose names match the specified regular expression are excluded from the check.';
