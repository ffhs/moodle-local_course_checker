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
 * Handles everything that has to do with the database.
 *
 * @package    local_course_checker
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_course_checker\db;

use dml_exception;

/**
 * Class database_manager
 *
 * Handles database operations related to tables outside local_course_checker.
 *
 * @package    local_course_checker
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class database_manager {
    /**
     * Retrieves planned adhoc tasks for a given class and course.
     *
     * @param string $classname The class name of the adhoc task.
     * @param int $courseid The course ID to filter tasks.
     * @param string $component The component name (default: 'local_course_checker').
     * @return array The list of planned adhoc tasks.
     * @throws dml_exception
     */
    public static function planned_adhoc_tasks(string $classname, int $courseid, string $component = 'local_course_checker'): array {
        global $DB;

        $sql = "SELECT * FROM {task_adhoc}
         WHERE classname = :classname
           AND component = :component
           AND customdata LIKE :courseid";

        $data = [
                'component' => $component,
                'classname' => $classname,
                'courseid' => '%' . $DB->sql_like_escape('"course":{"id":"' . $courseid . '"') . '%',
        ];

        return $DB->get_records_sql($sql, $data);
    }
}
