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
 * Settings for checking the course settings compared to a reference course
 *
 * @package    coursechecker_referencesettings
 * @copyright  2025 Simon Gisler, Fernfachhochschule Schweiz (FFHS) <simon.gisler@ffhs.ch>
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @copyright  based on work by 2019 Liip SA <elearning@liip.ch>
 * @copyright  based on work by 2019 Christoph Karlen, Fernfachhochschule Schweiz (FFHS) <christoph.karlen@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use coursechecker_referencesettings\checker;

defined('MOODLE_INTERNAL') || die();



/** @var admin_settingpage $settings */
$settings;

/** @var array of coursesettings_fields to check $choices */
$choices = [
        // General.
        'category' => get_string('category'),
        'visible' => get_string('visible'),
        'startdate' => get_string('startdate'),
        // Summary.
        'summary' => get_string('summary'),
        // Course Format.
        'format' => get_string('format'),
        // Appearance.
        'showgrades' => get_string('showgrades'),
        'newsitems' => get_string('newsitemsnumber'),
        'lang' => get_string('forcelanguage'),
        'showreports' => get_string('showreports'),
        // Files and uploads.
        'legacyfiles' => get_string('courselegacyfiles'),
        'maxbytes' => get_string('maximumupload'),
        // Completion Tracking.
        'enablecompletion' => get_string('enablecompletion', 'completion'),
        'groupmode' => get_string('groupmode'),
];

// Referencesettings Checker Checklist settings.
$visiblename = get_string('referencesettings_checklist', 'coursechecker_referencesettings');
$description = new lang_string('referencesettings_checklist_help', 'coursechecker_referencesettings');
$checklist = new admin_setting_configmulticheckbox(
    checker::REFERENCE_COURSE_SETTINGS,
    $visiblename,
    $description,
    checker::REFERENCE_COURSE_SETTINGS_DEFAULT,
    $choices
);
$settings->add($checklist);

// Referencesettings Checker Filter settings.
$visiblename = get_string('referencefilter_enabled', 'coursechecker_referencesettings');
$description = new lang_string('referencefilter_enabled_help', 'coursechecker_referencesettings');
$settings->add(new admin_setting_configcheckbox(
    checker::REFERENCE_COURSE_FILTER_ENABLED,
    $visiblename,
    $description,
    checker::REFERENCE_COURSE_FILTER_ENABLED_DEFAULT
));

// Referencesettings Checker Form Options settings.
$visiblename = get_string('referenceformatoptions_enabled', 'coursechecker_referencesettings');
$description = new lang_string('referenceformatoptions_enabled_help', 'coursechecker_referencesettings');
$settings->add(new admin_setting_configcheckbox(
    checker::REFERENCE_COURSE_FORMAT_OPTION_ENABLED,
    $visiblename,
    $description,
    checker::REFERENCE_COURSE_FORMAT_OPTION_ENABLED_DEFAULT
));
