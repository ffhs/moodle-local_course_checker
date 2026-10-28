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
class admin_setting_urllist extends admin_setting_configtextarea {
    /**
     * Normalizes the input data before saving it to the database.
     *
     * @param mixed $data
     * @return mixed
     * @throws coding_exception
     */
    public function write_setting($data): mixed {
        if (empty($data)) {
            return parent::write_setting($data);
        }

        $lines = array_filter(array_map('trim', explode("\n", $data)));
        $urls = [];

        foreach ($lines as $line) {
            if ($url = $this->normalize_url_entry($line)) {
                $urls[] = $url;
            } else {
                return get_string('invalidurl', 'error') . ': ' . $line;
            }
        }

        $urls = array_values(array_unique($urls));
        sort($urls, SORT_STRING);

        return parent::write_setting(implode("\n", $urls));
    }

    /**
     * Normalize and validate url
     *
     * @param string $input
     * @return string|false
     */
    protected function normalize_url_entry(string $input): string|false {
        if ($input === '') {
            return false;
        }
        $candidate = $input;

        if (preg_match('#^[a-z][a-z0-9+\-.]*://#i', $candidate)) {
            $scheme = strtolower((string) parse_url($candidate, PHP_URL_SCHEME));
            if ($scheme !== 'http' && $scheme !== 'https') {
                return false;
            }
        } else {
            $candidate = 'https://' . $candidate;
        }
        if ((str_contains($candidate, ' ') || !filter_var($candidate, FILTER_VALIDATE_URL))) {
            return false;
        }
        return $candidate;
    }
}
