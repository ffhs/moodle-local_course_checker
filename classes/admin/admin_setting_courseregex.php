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
 * This type of field should be used for config settings which contains a course fullname.
 *
 * @package    local_course_checker
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_course_checker\admin;

use coding_exception;
use local_course_checker\model\checker_config_trait;

/**
 * Class admin_setting_courseregex
 *
 * Defines an admin setting for validating course-related input using regular expressions.
 * This setting extends {@see admin_setting_regex} and integrates the {@see checker_config_trait}.
 */
class admin_setting_courseregex extends admin_setting_regex {
    use checker_config_trait;

    /**
     * Constructor for admin_setting_courseregex.
     *
     * Initializes the setting with a name, optional visible name, description, default value, parameter type, and size.
     *
     * @param string $name The unique name of the setting.
     * @param string|null $visiblename The display name of the setting (default: fetched from language string).
     * @param string|null $description The description of the setting (default: fetched from language string).
     * @param string $defaultsetting The default value for the setting.
     * @param int $paramtype The parameter type (default: PARAM_RAW).
     * @param int|null $size The size of the input field (optional).
     * @throws coding_exception
     */
    public function __construct($name, $visiblename = null, $description = null, $defaultsetting = '', $paramtype = PARAM_RAW, $size = null) {
        // Provide default values if $visiblename or $description are not set.
        $visiblename = $visiblename ?? get_string('admin_setting_coursesregex', 'local_course_checker');
        $description = $description ?? get_string('admin_setting_coursesregex_help', 'local_course_checker');

        parent::__construct($name, $visiblename, $description, $defaultsetting, $paramtype, $size);
    }
}
