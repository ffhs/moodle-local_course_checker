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
 * This type of field should be used for config settings which contains a regex string.
 *
 * @package    local_course_checker
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_course_checker\admin;

use admin_setting_configtext;
use coding_exception;
use local_course_checker\model\checker_config_trait;

/**
 * Class admin_setting_regex
 *
 * Defines an admin setting for validating regular expressions.
 * This setting extends {@see admin_setting_configtext} and integrates {@see checker_config_trait}.
 */
class admin_setting_regex extends admin_setting_configtext {
    use checker_config_trait;

    /**
     * Validates the provided regular expression.
     *
     * Ensures that the input is a valid regex pattern by checking against `preg_match`.
     *
     * @param string $data The regex pattern to validate.
     * @return true|string True if valid, otherwise an error message.
     * @throws coding_exception
     */
    public function validate($data) {
        // Check if the provided data is empty.
        if (empty($data)) {
            return true;
        }

        // Try to validate the regex pattern.
        try {
            if (@preg_match($data, '') === false) {
                return get_string('admin_setting_regex_invalidregex', 'local_course_checker');
            }
        } catch (\Throwable $e) {
            // Handle unexpected exceptions, though unlikely.
            return get_string('admin_setting_regex_invalidregex', 'local_course_checker');
        }

        // If everything is valid, return true.
        return true;
    }
}
