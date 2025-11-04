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
 * This file contains hooks and callbacks needed for the local_ffhs_course_checker plugin.
 *
 * @package     local_course_checker
 * @copyright   2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @copyright   2025 Simon Gisler, Fernfachhochschule Schweiz (FFHS) <simon.gisler@ffhs.ch>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * This function extends the course navigation with the course checker result page.
 *
 * @param navigation_node $navigation The navigation node to extend
 * @throws coding_exception
 */
function local_course_checker_extend_navigation_course(navigation_node $navigation): void {
    global $PAGE;

    if (!$context = $PAGE->context->get_course_context(false)) {
        return;
    }

    if ($context instanceof context_course && has_capability('local/course_checker:view_navigation', $context)) {
        $text = get_string('pluginname', 'local_course_checker');

        $selectmeetingnode = navigation_node::create(
            $text,
            new moodle_url('/local/course_checker/index.php', ['courseid' => $PAGE->course->id]),
            navigation_node::TYPE_SETTING,
            null,
            'course_checker',
            new pix_icon('i/role', $text)
        );
        $navigation->add_node($selectmeetingnode);
    }
}
