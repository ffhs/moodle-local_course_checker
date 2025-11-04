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
 *  Defines event observers for the local_course_checker plugin.
 *
 *  This file registers event handlers that respond to course and module events.
 *
 * @package    local_course_checker
 * @copyright  2023 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @copyright  based on work by 2019 Liip SA <elearning@liip.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
// List of observers.
$observers = [
        [
                'eventname' => '\core\event\course_updated', // Log when summary or name of the course changes.
                'callback' => '\local_course_checker\event_manager::course_event_trigger',
        ], [
                'eventname' => '\core\event\course_deleted', // Cleanup activity database table.
                'callback' => '\local_course_checker\event_manager::course_event_trigger',
        ],

        [
                'eventname' => '\core\event\course_module_created',
                'callback' => '\local_course_checker\event_manager::course_module_event_trigger',
        ], [
                'eventname' => '\core\event\course_module_deleted',
                'callback' => '\local_course_checker\event_manager::course_module_event_trigger',
        ], [
                'eventname' => '\core\event\course_module_updated',
                'callback' => '\local_course_checker\event_manager::course_module_event_trigger',
        ],

        [
                'eventname' => '\core\event\course_section_created',
                'callback' => '\local_course_checker\event_manager::course_section_event_trigger',
        ], [
                'eventname' => '\core\event\course_section_deleted',
                'callback' => '\local_course_checker\event_manager::course_section_event_trigger',
        ], [
                'eventname' => '\core\event\course_section_updated',
                'callback' => '\local_course_checker\event_manager::course_section_event_trigger',
        ],
];
