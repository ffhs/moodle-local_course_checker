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

namespace local_course_checker\db\model;

use core\persistent;

/**
 * Class check_result
 *
 * @package    local_course_checker
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class check_result extends persistent {
    /**
     * {@inheritDoc}
     */
    const string TABLE = 'local_course_checker_check_result';

    /**
     * {@inheritDoc}
     */
    protected static function define_properties() {
        return [
            'check_id' => ['type' => PARAM_INT],
            'status' => ['type' => PARAM_TEXT],
            'title' => ['type' => PARAM_TEXT, 'default' => null, 'null' => NULL_ALLOWED],
            'link' => ['type' => PARAM_TEXT, 'default' => null, 'null' => NULL_ALLOWED],
            'message' => ['type' => PARAM_TEXT, 'default' => null, 'null' => NULL_ALLOWED],
            'timestamp' => ['type' => PARAM_INT],
        ];
    }
}
