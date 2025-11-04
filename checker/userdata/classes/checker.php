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

namespace checker_userdata;

use local_course_checker\translation_manager;
use local_course_checker\mod_type_interface;
use local_course_checker\model\admin_setting_courseregex_trait;
use local_course_checker\model\check_plugin_interface;
use local_course_checker\db\model\check;
use local_course_checker\model\checker_config_trait;
use local_course_checker\resolution_link_helper;

/**
 * Checking if course contains user data in activities.
 *
 * @package    checker_userdata
 * @copyright  2025 Simon Gisler, Fernfachhochschule Schweiz (FFHS) <simon.gisler@ffhs.ch>
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @copyright  based on work by 2020 Adrian Perez, Fernfachhochschule Schweiz (FFHS) <adrian.perez@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class checker implements check_plugin_interface, mod_type_interface {
    use checker_config_trait;
    use admin_setting_courseregex_trait;

    /**
     * Runs the checker.
     *
     * @param \stdClass $course
     * @param check $check
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function run(\stdClass $course, check $check): void {

        // Initialize check result array.
        if (!$this->is_course_check_required('checker_userdata/userdata_coursesregex', $course)) {
            $title = translation_manager::generate('admin_setting_coursesregex_skip_course', 'local_course_checker');
            $message = translation_manager::generate('admin_setting_coursesregex_skip_course_desc', 'local_course_checker');
            $check->add_successful($title, '', $message);
            return;
        }

        // Get modules from checker setting that are allowed.
        $enabledmodules = explode(',', get_config('checker_userdata', 'userdata_modules'));
        // Get all activities in the course.
        $modinfo = get_fast_modinfo($course);
        foreach ($modinfo->cms as $cm) {
            // Skip activities that are not visible.
            if (!$cm->uservisible || !$cm->has_view()) {
                continue;
            }

            // Skip activity if is not allowed.
            if (!in_array($cm->modname, $enabledmodules)) {
                continue;
            }

            $title = resolution_link_helper::get_target($cm);
            $link = resolution_link_helper::get_link_to_modedit_or_view_page($cm->modname, $cm->id, false);

            $fetchuserdata = new fetch_userdata();
            $records = $fetchuserdata->check_for_userdata_in_module($cm);
            if (!empty($records)) {
                $message = translation_manager::generate('userdata_error', 'checker_userdata', $cm->modname);
                $check->add_warning($title, $link, $message);
                $check->set('status', 'warning');
                continue;
            }

            $message = translation_manager::generate('userdata_success', 'checker_userdata', $cm->modname);
            $check->add_successful($title, $link, $message);
        }
    }
}
