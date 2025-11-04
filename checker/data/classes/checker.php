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
 * Checking the group submission settings on
 * assignments for a course.
 *
 * @package    checker_data
 * @copyright  2025 Simon Gisler, Fernfachhochschule Schweiz (FFHS) <simon.gisler@ffhs.ch>
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @copyright  based on work by 2020 Christoph Karlen, Fernfachhochschule Schweiz (FFHS) <christoph.karlen@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace checker_data;

use local_course_checker\translation_manager;
use local_course_checker\mod_type_interface;
use local_course_checker\model\check_plugin_interface;
use local_course_checker\db\model\check;
use local_course_checker\model\checker_config_trait;
use local_course_checker\resolution_link_helper;
use stdClass;

/**
 * {@inheritDoc}
 */
class checker implements check_plugin_interface, mod_type_interface {
    use checker_config_trait;

    /**
     * @var check The result of the check.
     */
    protected check $check;

    /**
     * Runs the checker.
     *
     * @param stdClass $course
     * @param check $check
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function run(stdClass $course, check $check): void {
        global $DB;

        $this->check = $check;

        // Get all database activities in the course.
        $modinfo = get_fast_modinfo($course);
        $cms = $modinfo->get_instances_of(self::MOD_TYPE_DATA);
        foreach ($cms as $cm) {
            // Skip activities that are not visible.
            if (!$cm->uservisible || !$cm->has_view()) {
                continue;
            }

            $countfields = $DB->count_records('data_fields', ['dataid' => $cm->instance]);
            $titel = resolution_link_helper::get_target($cm);
            $link = resolution_link_helper::get_link_to_modedit_or_view_page($cm->modname, $cm->id, false);

            if ($countfields == 0) {
                $message = translation_manager::generate('nofieldindatabase', self::MOD_TYPE_DATA);
                $check->add_failed($titel, $link, $message);
                $check->set('status', 'failed');
                continue;
            }

            $message = translation_manager::generate('data_success', 'checker_data');
            $check->add_successful($titel, $link, $message);
        }
    }
}
