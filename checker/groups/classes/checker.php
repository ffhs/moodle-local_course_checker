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
 * @package    checker_groups
 * @copyright  2025 Simon Gisler, Fernfachhochschule Schweiz (FFHS) <simon.gisler@ffhs.ch>
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @copyright  based on work by 2019 Liip SA <elearning@liip.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace checker_groups;

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

        // Get all assignment activities in the course.
        $modinfo = get_fast_modinfo($course);
        $cms = $modinfo->get_instances_of(self::MOD_TYPE_ASSIGN);
        foreach ($cms as $cm) {
            // Skip activities that are not visible.
            if (!$cm->uservisible || !$cm->has_view()) {
                continue;
            }
            $title = resolution_link_helper::get_target($cm, 'checker_groups');
            $link = resolution_link_helper::get_link_to_modedit_or_view_page($cm->modname, $cm->id);
            // Get the assignment record from the assignment table.
            // The instance of the course_modules table is used as a foreign key to the assign table.
            $assign = $DB->get_record(
                'assign',
                ['course' => $course->id, 'id' => $cm->instance]
            );
            // Get the settings from the assign table: these are the settings used for group submission.
            $groupmode = $assign->teamsubmission;
            $groupingid = $assign->teamsubmissiongroupingid;
            // Now the groups settings can be checked.
            // These are the settings of assignment group submission in the corresponding activity.
            // Case 1: the group mode is deactivated -> check okay.
            if ($groupmode == 0) {
                $message = translation_manager::generate('groups_deactivated', 'checker_groups');
                $check->add_successful($title, $link, $message);
                continue;
            }

            // Case 2: the group mode is activated.
            // If the groupingid is not set -> check fails.
            if ($groupingid == 0) {
                $message = translation_manager::generate('groups_idmissing', 'checker_groups');
                $check->add_failed($title, $link, $message);
                $check->set('status', 'failed');
                continue;
            }
            // If the grouping does not exist -> check fails.
            $groupingexists = $DB->record_exists('groupings', ['id' => $groupingid]);
            if (!$groupingexists) {
                $message = translation_manager::generate('groups_missing', 'checker_groups');
                $check->add_failed($title, $link, $message);
                $check->set('status', 'failed');
                continue;
            }
            // If the grouping has less then 2 groups -> check fails.
            $groupcount = $DB->count_records('groupings_groups', ['groupingid' => $groupingid]);
            if ($groupcount < 2) {
                $message = translation_manager::generate('groups_lessthantwogroups', 'checker_groups');
                $check->add_failed($title, $link, $message);
                $check->set('status', 'failed');
                continue;
            }
            // The group submission is activated and all checks have passed -> check okay.
            $message = translation_manager::generate('groups_success', 'checker_groups');
            $check->add_successful($title, $link, $message);
        }
    }
}
