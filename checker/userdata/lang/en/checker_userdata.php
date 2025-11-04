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
 * Strings for component 'checker_userdata'.
 *
 * @package    checker_userdata
 * @copyright  2025 Simon Gisler, Fernfachhochschule Schweiz (FFHS) <simon.gisler@ffhs.ch>
 * @copyright  based on work by 2019 Liip SA <elearning@liip.ch>
 * @copyright  based on work by 2019 Adrian Perez, Fernfachhochschule Schweiz (FFHS) <adrian.perez@ffhs.ch>
 * @copyright  based on work by 2020 Christoph Karlen, Fernfachhochschule Schweiz (FFHS) <christoph.karlen@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'User data check';
$string['pluginname_help'] = 'Checks course activities for residual user data such as submissions, forum posts, or logs.';

// String specific for the userdata checker.
$string['userdata_setting_modules'] = 'Enabled modules';
$string['userdata_setting_modules_help'] =
        'Define the allowed modules (must be enabled in <a href="/admin/modules.php" target="_blank">Manage activities</a>, contain reset_userdata method in <code>mod/{modname}/lib.php</code> and supported by this plugin) to be checked for user data.';
$string['userdata_error'] = 'There shouldn\'t be any user data in the {$a} activity.';
$string['userdata_success'] = 'The {$a} activity contains no user data.';
$string['userdata_help'] =
        'If you want this data to be copied to other courses, you have to import it manually. Here are some useful manuals: <a href="https://docs.moodle.org/38/en/Backup_of_user_data" target="_blank">Backup of user data</a> and <a href="https://docs.moodle.org/38/en/Reusing_activities" target="_blank">Reusing activities</a>.';
