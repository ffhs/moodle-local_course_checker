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

namespace local_course_checker;

use core\event\base;
use dml_exception;
use local_course_checker\db\model\checker;
use local_course_checker\db\model\event;

/**
 * Event manager for handling Moodle course and module events.
 *
 * This class listens for course, module, and section-related events and triggers
 * appropriate actions such as logging, cleanup, or custom processing.
 *
 * @package    local_course_checker
 * @category   event
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @copyright  2025 Simon Gisler, Fernfachhochschule Schweiz (FFHS) <simon.gisler@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class event_manager {
    /**
     * Handles course-related events and saves them in the database.
     *
     * @param base $event The Moodle event object.
     * @throws dml_exception
     */
    public static function course_event_trigger(base $event): void {
        if ($event->eventname === '\core\event\course_deleted') {
            $checker = checker::get_record(['course_id' => $event->courseid]);
            if ($checker) {
                $checker->delete();
            }
            return;
        }
        self::log_event($event);
    }

    /**
     * Handles course module events and saves them in the database.
     *
     * @param base $event The Moodle event object.
     * @throws dml_exception
     */
    public static function course_module_event_trigger(base $event): void {
        self::log_event($event);
    }

    /**
     * Handles course section events and saves them in the database.
     *
     * @param base $event The Moodle event object.
     * @throws dml_exception
     */
    public static function course_section_event_trigger(base $event): void {
        self::log_event($event);
    }

    /**
     * Logs an event into the local_course_checker_event table.
     *
     * @param base $event The Moodle event object.
     * @throws dml_exception
     */
    private static function log_event(base $event): void {
        $checker = checker::get_record(['course_id' => $event->courseid, 'version_name' => 'latest']);
        if ($checker) {
            // Insert event into the database.
            event::create_from_event($checker->get('id'), $event);
        }
    }
}
