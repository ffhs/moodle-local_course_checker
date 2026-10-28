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
 * Checking quizzes inside the course if the sumgrade matches the grade.
 *
 * @package    coursechecker_quiz
 * @copyright  2026 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use coursechecker_quiz\checker;
use local_course_checker\admin\admin_setting_regex;

defined('MOODLE_INTERNAL') || die();

/** @var admin_settingpage $settings */

$settings->add(new admin_setting_regex(
    checker::WHITELISTREGEX,
    get_string('regex_whitelist', 'coursechecker_quiz'),
    get_string('regex_whitelist_desc', 'coursechecker_quiz'),
    checker::WHITELISTREGEX_DEFAULT
));
