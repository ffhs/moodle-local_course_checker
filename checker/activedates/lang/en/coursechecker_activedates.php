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
$string['pluginname_help'] = 'This plugin checks whether a course has both a start date and an end date defined. It is designed to be part of a course quality assurance workflow, ensuring that all courses have clearly set temporal boundaries.';
$string['privacy:metadata'] = 'The active dates check does not store any personal data. The check results are stored in the course checker plugin.';
// String specific for the activedates checker.
$string['activedates_setting_modules'] = 'Enabled modules';
$string['activedates_setting_modules_help'] =
    'Define the allowed modules (must be enabled in <a href="{$a}" target="_blank">Manage activities</a>) to be checked for active dates.';
$string['activedates_noactivedates'] = 'There shouldn\'t be enabled dates in the "activity completion" section.';
$string['activedates_noactivedatesinactivity'] =
    'There shouldn\'t be enabled dates in the {$a->modtype} activity, look for the following fields: {$a->adateissetin}';
$string['activedates_success'] = 'The {$a} activity is configured correctly';
