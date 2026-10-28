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
 * Strings for component 'coursechecker_activedates'.
 *
 * @package    coursechecker_activedates
 * @copyright  2025 Simon Gisler, Fernfachhochschule Schweiz (FFHS) <simon.gisler@ffhs.ch>
 * @copyright  based on work by 2019 Liip SA <elearning@liip.ch>
 * @copyright  based on work by 2019 Adrian Perez, Fernfachhochschule Schweiz (FFHS) <adrian.perez@ffhs.ch>
 * @copyright  based on work by 2020 Christoph Karlen, Fernfachhochschule Schweiz (FFHS) <christoph.karlen@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


$string['pluginname'] = 'Active dates check';
$string['pluginname_help'] = 'This check examines visible, viewable activities of the configured types and reports enabled completion dates or activity-specific availability and deadline fields. Courses excluded by the configured course-name pattern, unconfigured activity types, and hidden or non-viewable activities are not checked.';
$string['privacy:metadata'] = 'This check does not store any personal data. Its results are stored by the Course Checker plugin.';
// String specific for the activedates checker.
$string['activedates_setting_modules'] = 'Enabled modules';
$string['activedates_setting_modules_help'] = 'Define the allowed modules (must be enabled in <a href="{$a}" target="_blank">Manage activities</a>) to be checked for active dates.';
$string['activedates_noactivedates'] = 'The expected completion date must not be enabled.';
$string['activedates_noactivedatesinactivity'] = 'The <b>{$a->modtype}</b> activity has enabled dates in the following fields: <b>{$a->adateissetin}</b>.';
$string['activedates_success'] = 'The {$a} activity has no enabled dates.';
