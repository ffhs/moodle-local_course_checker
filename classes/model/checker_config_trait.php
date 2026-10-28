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

namespace local_course_checker\model;

use local_course_checker\config;

/**
 *
 * Used to simplify the reading of config values
 *
 * @package    local_course_checker
 * @copyright  2025 Simon Gisler, Fernfachhochschule Schweiz (FFHS) <simon.gisler@ffhs.ch>
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @copyright  based on work by 2019 Liip SA <elearning@liip.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait checker_config_trait {
    /**
     * Read a setting based on plugin name.
     *
     * @param string $name
     * @param mixed|null $defaultvalue
     * @return mixed|null
     * @deprecated Use {@see \local_course_checker\config::get()} instead.
     */
    protected function get_config($name, $defaultvalue = null): mixed {
        debugging(
            'get_config() is deprecated. Use local_course_checker\\config::get() instead.',
            DEBUG_DEVELOPER,
        );

        return config::get($name, $defaultvalue);
    }
}
