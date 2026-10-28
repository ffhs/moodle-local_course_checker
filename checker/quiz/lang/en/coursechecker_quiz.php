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
 * Strings for component 'coursechecker_quiz'.
 *
 * @package    coursechecker_quiz
 * @copyright  2025 Simon Gisler, Fernfachhochschule Schweiz (FFHS) <simon.gisler@ffhs.ch>
 * @copyright  based on work by 2019 Liip SA <elearning@liip.ch>
 * @copyright  based on work by 2019 Adrian Perez, Fernfachhochschule Schweiz (FFHS) <adrian.perez@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Quiz check';
$string['pluginname_help'] = 'This check compares the maximum grade with the total question marks for every quiz in the course. Quizzes whose names match the configured whitelist regular expression are not compared.';
$string['privacy:metadata'] = 'This check does not store any personal data. Its results are stored by the Course Checker plugin.';

$string['regex_whitelist'] = 'Regex quiz title whitelist';
$string['regex_whitelist_desc'] = 'Enter a regex pattern. Quizzes matching this title pattern will be whitelisted and skip the check.';
$string['quiz_in_whitelist'] = 'The quiz was not verified because it matches the whitelist pattern.';
$string['quiz_grade_sum_error'] = 'Maximum grade ({$a->grade}) and Total of marks ({$a->sumgrades}) should be the same number in this quiz';
$string['quiz_grade_sum_success'] = 'This quiz is configured correctly';
$string['quiz_activity'] = 'Activity: {$a->name}  ({$a->modname})';
