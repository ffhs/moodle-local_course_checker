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
 * This type of field should be used for config settings which contain an url.
 *
 * @package    local_course_checker
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_course_checker\admin;

use admin_setting_configtext;

/**
 * Class admin_setting_url
 *
 * Defines an admin setting for handling and validating URLs.
 * This setting extends {@see admin_setting_configtext} and ensures that URLs are properly formatted.
 */
class admin_setting_url extends admin_setting_configtext {
    /**
     * Processes and writes the URL setting value.
     *
     * - Trims whitespace from the input.
     * - Uses the default setting if the input is empty.
     * - Ensures the URL starts with `https://` or `http://`.
     * - Ensures the URL ends with a trailing slash (`/`).
     *
     * @param string $data The input URL from the admin setting.
     * @return string The formatted setting value to be stored.
     */
    public function write_setting($data) {
        $data = trim($data);

        if (empty($data)) {
            $data = $this->defaultsetting;
            return parent::write_setting($data);
        }

        // Ensure the URL starts with 'http://' or 'https://'.
        if (!preg_match('/^http:\/\/|^https:\/\//', $data)) {
            $data = 'https://' . $data;
        }

        // Ensure the URL ends with a trailing slash.
        if (!preg_match('/\/$/', $data)) {
            $data = $data . '/';
        }

        return parent::write_setting($data);
    }
}
