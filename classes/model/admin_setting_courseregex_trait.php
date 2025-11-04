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

namespace local_course_checker\model;

use stdClass;

/**
 * Trait to determine whether a course check should be run based on a configurable regular expression.
 *
 * This trait uses a plugin configuration setting (a regex) to check whether a given course's fullname
 * matches the specified pattern. Intended to be reused across admin settings and checker implementations.
 *
 * @package    local_course_checker
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait admin_setting_courseregex_trait {
    use checker_config_trait;

    /**
     * Determines if a course should be checked based on a configured regex pattern.
     *
     * The method looks up a regex pattern from the plugin configuration using the provided name.
     * If found, it checks whether the course's fullname matches the regex.
     *
     * @param string $name The full config name in the format 'plugin/settingname'.
     * @param stdClass $course The course object to validate.
     * @return bool True if the course should be checked, false otherwise.
     */
    protected function is_course_check_required(string $name, stdClass $course): bool {
        if (!empty($this->get_config($name))) {
            // If the configuration value exists, validate the course fullname against the regex.
            return (bool)preg_match($this->get_config($name), $course->fullname);
        }
        // If the configuration value does not exist, return false.
        return true;
    }
}
