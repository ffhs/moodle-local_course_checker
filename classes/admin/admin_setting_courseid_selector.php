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
 * This type of field should be used for config settings which contains a courseid.
 *
 * @package    local_course_checker
 * @copyright  2025 Simon Gisler, Fernfachhochschule Schweiz (FFHS) <simon.gisler@ffhs.ch>
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @copyright  based on work by 2019 Liip SA <elearning@liip.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_course_checker\admin;

use coding_exception;
use dml_exception;

/**
 * Class representing an admin setting for selecting a course ID.
 *
 * This setting ensures the selected course ID exists in the system.
 * It extends {@see admin_setting_restrictedint} to enforce integer validation.
 */
class admin_setting_courseid_selector extends admin_setting_restrictedint {
    /**
     * Validates the given course ID.
     *
     * Ensures the value is an integer and that the corresponding course exists.
     *
     * {@inheritDoc}
     *
     * @param mixed $data The value to validate.
     * @return true|string True if valid, otherwise an error message.
     * @throws coding_exception
     */
    public function validate($data) {
        // Be sure the value is an int.
        $validate = parent::validate($data);
        if ($validate !== true) {
            return $validate;
        }

        // Load the course to be sure it exists.
        try {
            get_course($data, false);
            return true;
        } catch (dml_exception $exception) {
            return get_string("cannotfindcourse", 'error');
        }
    }
}
