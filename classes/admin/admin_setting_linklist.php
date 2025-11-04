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
 * This type of field should be used for config settings which contains domains and links.
 *
 * @package    local_course_checker
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @copyright  based on work by 2019 Liip SA <elearning@liip.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_course_checker\admin;

use admin_setting_configtextarea;
use coding_exception;

/**
 * Class admin_setting_linklist
 *
 * Defines an admin setting for managing a list of links.
 * This setting extends {@see admin_setting_configtextarea} and validates input as a list of domain names.
 */
class admin_setting_linklist extends admin_setting_configtextarea {
    /**
     * Validates the provided list of URLs.
     *
     * Ensures that each line in the textarea contains a valid domain name.
     *
     * {@inheritDoc}
     *
     * @param mixed $data The input data to validate.
     * @return true|string True if valid, otherwise an error message.
     * @throws coding_exception If validation encounters an unexpected issue.
     */
    public function validate($data) {
        if (empty($data)) {
            return true;
        }

        $urls = array_filter(array_map('trim', explode("\n", $data)));

        foreach ($urls as $url) {
            if (!$this->is_valid_url($url)) {
                return get_string('invalidurl', 'error') . ': ' . $url;
            }
        }

        return true;
    }

    /**
     * Check one url whether it is valid.
     * - Allows http/https
     * - Allows Domain/IP/localhost
     * - Allows Ports
     * - Allows Paths
     *
     * @param string $url
     * @return bool
     */
    protected function is_valid_url(string $url): bool {
        return (1 ===
            preg_match("/^(https?:\/\/)?(((localhost)|(([a-z\d](-*[a-z\d])*\.)+[a-z]{1,}))|(\d{1,3}(\.\d{1,3}){3}))(:\d{1,5})?(\/[^\s]*)?$/i", $url)
        );
    }
}
