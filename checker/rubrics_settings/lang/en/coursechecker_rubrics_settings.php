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
 * Strings for component 'coursechecker_rubrics_settings'.
 *
 * @package     coursechecker_rubrics_settings
 * @copyright   2026 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Rubric settings check';
$string['pluginname_help'] = 'This check examines assignments for which a rubric is enabled as the advanced grading method. The grade type must be set to points rather than a scale. An active rubric grading form must also be available. If the form is missing or still in draft status, a warning is issued. If the maximum rubric score does not match the maximum grade in the assignment settings, an error is reported. Assignments without an active rubric are not checked.';
$string['privacy:metadata'] = 'This check does not store any personal data. Its results are stored by the Course Checker plugin.';

$string['rubric_grade_type_scale'] = 'When using a rubric, the grade type must be "Points". A scale is currently selected.';
$string['rubric_grade_type_none'] = 'When using a rubric, the grade type must be set to points.';
$string['rubric_points_mismatch'] = 'The maximum rubric score ({$a->rubric_max}) does not match the maximum grade in the assignment settings ({$a->assign_grade}).';
$string['rubric_points_success'] = 'The rubric points match the assignment settings.';
