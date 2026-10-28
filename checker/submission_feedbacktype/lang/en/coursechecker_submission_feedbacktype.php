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
 * Strings for component 'coursechecker_submission_feedbacktype'.
 *
 * @package    coursechecker_submission_feedbacktype
 * @copyright  2026 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Assignment feedback type check';
$string['pluginname_help'] = 'This check examines assignments with an enabled Online text, File submissions, or Kaltura video submission type and requires at least one enabled feedback type. Assignments without any of these three submission types are ignored; other enabled submission types alone do not trigger the check.';
$string['privacy:metadata'] = 'This check does not store any personal data. Its results are stored by the Course Checker plugin.';
$string['submission_feedbacktype_error'] = 'The assignment has submission types enabled, but no feedback type is enabled. Please enable at least one feedback type.';
$string['submission_feedbacktype_success'] = 'The assignment has at least one feedback type enabled.';
