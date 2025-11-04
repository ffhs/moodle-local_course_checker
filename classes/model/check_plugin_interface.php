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
 * Checker Plugin Interface
 *
 * @package    local_course_checker
 * @copyright  2024 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_course_checker\model;

use local_course_checker\db\model\check;
use stdClass;

/**
 * This is an interface made to run a single check.
 */
interface check_plugin_interface {
    /**
     * Runs the checker.
     *
     * @param stdClass $course
     * @param check $check
     * @return void
     */
    public function run(stdClass $course, check $check): void;
}
