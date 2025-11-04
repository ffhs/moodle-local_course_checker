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

use coding_exception;
use core\event\base;
use core\persistent;
/**
 * Persistent model for storing course-related event logs from the checker.
 *
 * This model maps to the 'local_course_checker_event' table and allows storage of
 * Moodle events.
 *
 * @package    local_course_checker
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class event extends persistent {
    /**
     * {@inheritDoc}
     */
    const TABLE = 'local_course_checker_event';

    /**
     * {@inheritDoc}
     */
    protected static function define_properties(): array {
        return [
            'checker_id' => ['type' => PARAM_INT],
            'eventname' => ['type' => PARAM_TEXT],
            'component' => ['type' => PARAM_COMPONENT],
            'action' => ['type' => PARAM_ALPHANUMEXT],
            'target' => ['type' => PARAM_ALPHANUMEXT],
            'objecttable' => ['type' => PARAM_ALPHANUMEXT, 'default' => null, 'null' => NULL_ALLOWED],
            'objectid' => ['type' => PARAM_INT, 'default' => null, 'null' => NULL_ALLOWED],
            'crud' => ['type' => PARAM_ALPHA],
            'edulevel' => ['type' => PARAM_INT],
            'contextid' => ['type' => PARAM_INT],
            'contextlevel' => ['type' => PARAM_INT],
            'contextinstanceid' => ['type' => PARAM_INT],
            'userid' => ['type' => PARAM_INT],
            'courseid' => ['type' => PARAM_INT, 'default' => null, 'null' => NULL_ALLOWED],
            'relateduserid' => ['type' => PARAM_INT, 'default' => null, 'null' => NULL_ALLOWED],
            'anonymous' => ['type' => PARAM_INT, 'default' => 0],
            'other' => [
                'type' => PARAM_RAW, // Since it holds serialized or json data.
                'default' => null,
                'null' => NULL_ALLOWED,
            ],
            'timecreated' => ['type' => PARAM_INT],
        ];
    }

    /**
     * Creates and persists a new course checker event based on a core Moodle event.
     *
     * Extracts all relevant data from the provided base event object and stores it
     * into the custom checker event table using the persistent API.
     *
     * @param int $checkerid The ID of the parent checker record.
     * @param base $baseevent The Moodle event to extract data from.
     * @return self The newly created persistent event object.
     * @throws coding_exception If required fields are missing.
     */
    public static function create_from_event(int $checkerid, base $baseevent): self {
        $event = new self();
        $event->set('checker_id', $checkerid);
        $event->set('eventname', $baseevent->eventname);
        $event->set('component', $baseevent->component);
        $event->set('action', $baseevent->action);
        $event->set('target', $baseevent->target);
        $event->set('objecttable', $baseevent->objecttable);
        $event->set('objectid', $baseevent->objectid);
        $event->set('crud', $baseevent->crud);
        $event->set('edulevel', $baseevent->edulevel);

        // Context info.
        $context = $baseevent->get_context();
        $event->set('contextid', $context->id);
        $event->set('contextlevel', $context->contextlevel);
        $event->set('contextinstanceid', $context->instanceid);

        // User info.
        $event->set('userid', $baseevent->userid);
        $event->set('courseid', $baseevent->courseid);
        $event->set('relateduserid', $baseevent->relateduserid);
        $event->set('anonymous', $baseevent->anonymous);

        // Other data.
        $event->set('other', !empty($baseevent->other) ? json_encode($baseevent->other) : json_encode(null));

        $event->set('timecreated', $baseevent->timecreated);
        $event->create();

        return $event;
    }
}
