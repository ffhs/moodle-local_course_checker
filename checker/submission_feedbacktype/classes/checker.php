<?php
// This file is part of Moodle - https://moodle.org/
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
 * Check if submission activities have set a feedbacktype.
 *
 * @package    coursechecker_submission_feedbacktype
 * @copyright  2026 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace coursechecker_submission_feedbacktype;

use coding_exception;
use core\exception\moodle_exception;
use core\invalid_persistent_exception;
use dml_exception;
use local_course_checker\db\model\check;
use local_course_checker\mod_type_interface;
use local_course_checker\model\check_plugin_interface;
use local_course_checker\resolution_link_helper;
use local_course_checker\translation_manager;
use moodle_url;
use stdClass;

/**
 * {@inheritDoc}
 */
class checker implements check_plugin_interface, mod_type_interface {
    /**
     * Runs the checker.
     *
     * @param stdClass $course
     * @param check $check
     * @return void
     * @throws coding_exception
     * @throws moodle_exception
     * @throws invalid_persistent_exception
     * @throws dml_exception
     */
    public function run(stdClass $course, check $check): void {
        global $DB;
        // Get all assign activities in the course.
        $modinfo = get_fast_modinfo($course);
        $assignments = get_all_instances_in_courses(self::MOD_TYPE_ASSIGN, [$course->id => $course]);
        if (empty($assignments)) {
            return;
        }
        foreach ($assignments as $assignment) {
            $cm = $modinfo->get_cm($assignment->coursemodule);
            $title = resolution_link_helper::get_target($cm);
            $resolutionlink = (new moodle_url('/course/modedit.php', ['update' => $cm->id]))->out(false);

            $sql = "SELECT * FROM {assign_plugin_config}
                    WHERE assignment = :assignment
                    AND subtype = :subtype
                    AND name = :name
                    AND " . $DB->sql_compare_text('value') . " = :value";

            $submissionparams = [
                'assignment' => $assignment->id,
                'subtype' => 'assignsubmission',
                'name' => 'enabled',
                'value' => '1',
            ];

            // Restrict to subplugins to onlinetext, file or kalvid.
            $hassubmission = $DB->get_record_sql($sql . "AND plugin IN ('onlinetext', 'file', 'kalvid')", $submissionparams);
            if ($hassubmission) {
                $feedbackparams = [
                    'assignment' => $assignment->id,
                    'subtype' => 'assignfeedback',
                    'name' => 'enabled',
                    'value' => '1',
                ];
                $hasfeedback = $DB->get_record_sql($sql, $feedbackparams);
                if (!$hasfeedback) {
                    $message = translation_manager::generate(
                        'submission_feedbacktype_error',
                        'coursechecker_submission_feedbacktype'
                    );
                    $check->add_failed($title, $resolutionlink, $message);
                } else {
                    $message = translation_manager::generate(
                        'submission_feedbacktype_success',
                        'coursechecker_submission_feedbacktype'
                    );
                    $check->add_successful($title, $resolutionlink, $message);
                }
            }
        }
    }
}
