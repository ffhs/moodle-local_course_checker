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

namespace local_course_checker\output;

use core_user;
use local_course_checker\db\model\event as event_model;
use moodle_exception;
use moodle_url;
use renderable;
use renderer_base;
use stdClass;
use templatable;
use core\event\base;
use core\context;
use html_writer;
use function fullname;

/**
 * Renderable and templatable wrapper for checker event data.
 *
 * Converts persisted event data back into a Moodle event object (via base::restore)
 * and provides templated rendering output.
 *
 * @package    local_course_checker
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class event implements renderable, templatable {
    /**
     * @var event_model The persistent model representing a stored course checker event.
     */
    private event_model $event;

    /**
     * event constructor.
     *
     * @param event_model $event The persistent event record.
     */
    public function __construct(event_model $event) {
        $this->event = $event;
    }


    /**
     * Prepares the event data for use in a Mustache template.
     *
     * @param renderer_base $output The renderer instance to generate icons and links.
     * @return stdClass A template-compatible object containing event display data.
     * @throws moodle_exception
     */
    public function export_for_template(renderer_base $output) {
        $event = (array)$this->event->to_record();
        // Unset what base::restore() doesn't need.
        unset($event["checker_id"]);
        unset($event["id"]);
        unset($event["timemodified"]);
        unset($event["usermodified"]);
        // Restore Json.
        $event['other'] = json_decode($event['other'], true);
        $restoredevent = base::restore($event, []);
        $actionlink = $this->get_action_icon($restoredevent->action, $output) . ' ' . $restoredevent->get_name_with_info();
        $actionlink = html_writer::link($restoredevent->get_url(), $actionlink, ['target' => '_blank']);

        $context = context::instance_by_id($restoredevent->contextid, IGNORE_MISSING);
        if ($context) {
            $contextname = $context->get_context_name();
            if ($cm = get_coursemodule_from_id(null, $restoredevent->contextinstanceid, $restoredevent->courseid)) {
                $modname = $cm->modname;
                $icon = $output->pix_icon('icon', get_string('pluginname', 'mod_' . $modname), 'mod_' . $modname);
                $contextname = $icon . ' ' . $contextname;
            }
            if ($url = $context->get_url()) {
                $contextname = html_writer::link($url, $contextname, ['target' => '_blank']);
            }
        } else {
            $contextname = get_string('other');
        }

        if ($userid = $restoredevent->userid) {
            $user = core_user::get_user($userid);
            if ($user && !$user->deleted) {
                $username = fullname($user);
                $userurl = new moodle_url('/user/view.php', ['id' => $userid]);
                $userlink = html_writer::link($userurl, $username, ['target' => '_blank']);
            } else {
                $userlink = get_string('userdeleted', 'core');
            }
        } else {
            $userlink = get_string('unknownuser', 'core');
        }

        return (object)[
            'action' => $actionlink,
            'user' => $userlink,
            'context' => $contextname,
            'timestamp' => userdate($restoredevent->timecreated, '%A, %x %R'),
        ];
    }

    /**
     * Returns a pix icon corresponding to an event action.
     *
     * @param string $action The action type ('created', 'updated', 'deleted', etc.).
     * @param renderer_base $output Renderer used to generate the icon.
     * @return string HTML string representing the icon.
     */
    private function get_action_icon(string $action, renderer_base $output): string {
        switch ($action) {
            case 'created':
                $iconname = 't/add';
                break;
            case 'updated':
                $iconname = 'i/edit';
                break;
            case 'deleted':
                $iconname = 't/delete';
                break;
            default:
                $iconname = 'docs';
        }
        return $output->pix_icon($iconname, $action);
    }
}
