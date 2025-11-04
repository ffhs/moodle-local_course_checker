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
 * This type of field should be used for the question duplicate comparison rules.
 *
 * @package    local_course_checker
 * @copyright  2022 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_course_checker\admin;

use admin_setting_configtextarea;

/**
 * Class admin_setting_question_compare_rules
 *
 * Defines an admin setting for configuring question comparison rules.
 * This setting extends {@see admin_setting_configtextarea} and ensures that input follows a specific `key => value` format.
 */
class admin_setting_question_compare_rules extends admin_setting_configtextarea {
    /**
     * Processes and writes the setting value.
     *
     * Ensures that each line follows the `key => value` format by trimming spaces and correcting whitespace issues.
     *
     * @param string $data The raw input data from the textarea.
     * @return string The formatted setting value to be stored.
     */
    public function write_setting($data) {
        $lines = explode("\n", $data);
        $values = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (preg_match('/\s*([^=]+)\s*=>\s*([^=]+)\s*/', $line, $matches)) {
                $values[] = trim($matches[1]) . ' => ' . trim($matches[2]);
            }
        }

        return parent::write_setting(implode("\n", $values));
    }
}
