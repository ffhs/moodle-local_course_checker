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
 * Strings for component 'coursechecker_referencesettings'.
 *
 * @package    coursechecker_referencesettings
 * @copyright  2025 Simon Gisler, Fernfachhochschule Schweiz (FFHS) <simon.gisler@ffhs.ch>
 * @copyright  based on work by 2019 Liip SA <elearning@liip.ch>
 * @copyright  based on work by 2019 Adrian Perez, Fernfachhochschule Schweiz (FFHS) <adrian.perez@ffhs.ch>
 * @copyright  based on work by 2020 Christoph Karlen, Fernfachhochschule Schweiz (FFHS) <christoph.karlen@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Reference settings check';
$string['pluginname_help'] = 'This plugin compares general course settings (such as category, format, language, filters, and format options) of the current course with those of a reference course.';
$string['privacy:metadata'] = 'The reference settings check does not store any personal data. The check results are stored in the course checker plugin.';

// String specific for the reference course settings checker.
$string['referencesettings_comparison'] =
        ' (Reference course: "{$a->settingvaluereference}" | Current course: "{$a->settingvaluecurrent}")';
$string['referencesettings_settingismissing'] = 'The "{$a->setting}" is not a coursesetting';
$string['referencesettings_failing'] = 'The setting "{$a->setting}" is not correct {$a->comparison}';
$string['referencesettings_success'] = 'The setting "{$a->setting}" is correct';
$string['referencesettings_checklist'] = 'Reference course checker settings checklist';
$string['referencesettings_checklist_help'] = 'Please select one or multiple settings to check with the reference course.';

// String specific for the reference course settings checker filters.
$string['referencefilter_comparison'] =
        ' (Reference course: "{$a->filtervaluereference}" | Current course: "{$a->filtervaluecurrent}")';
$string['referencefilter_failing'] = 'The filter "{$a->filterkey}" is not correct {$a->comparison}';
$string['referencefilter_success'] = 'All filters are correctly set in current course';
$string['referencefilter_enabled'] = 'Reference settings filter check enabled';
$string['referencefilter_enabled_help'] = 'Please enable this to compare all course filter with the reference course.';
$string['referencefilter_filternotsetincurrentcourse'] = 'The filter "{$a->filterkey}" is missing in the current course.';
$string['referenceformatoptions_failing'] = 'The format option "{$a->optionkey}" is not correct {$a->comparison}';
$string['referenceformatoptions_success'] = 'All format options are correctly set in current course';
$string['referenceformatoptions_enabled'] = 'Reference settings format options check enabled';
$string['referenceformatoptions_enabled_help'] =
        'Please enable this to compare all course format options with the reference course.';
