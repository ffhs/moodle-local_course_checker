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
 * Checks rubric specific settings: grade type and points match.
 *
 * @package     coursechecker_rubrics_settings
 * @copyright   2026 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace coursechecker_rubrics_settings;

use core\exception\moodle_exception;
use core\invalid_persistent_exception;
use local_course_checker\db\model\check;
use local_course_checker\mod_type_interface;
use local_course_checker\model\check_plugin_interface;
use local_course_checker\resolution_link_helper;
use local_course_checker\translation_manager;
use moodle_url;
use stdClass;

defined('MOODLE_INTERNAL') || die();

/**
 * Class checker
 */
class checker implements check_plugin_interface, mod_type_interface {
    /**
     * Runs the checker.
     *
     * @param stdClass $course
     * @param check $check
     * @return void
     * @throws \coding_exception
     * @throws moodle_exception
     * @throws invalid_persistent_exception
     */
    public function run(stdClass $course, check $check): void {
        global $CFG;
        require_once($CFG->dirroot . '/grade/grading/lib.php');
        // Get all assign activities in the course.
        $modinfo = get_fast_modinfo($course);
        $assignments = get_all_instances_in_courses(self::MOD_TYPE_ASSIGN, [$course->id => $course]);
        if (empty($assignments)) {
            return;
        }

        foreach ($assignments as $assignment) {
            $cm = $modinfo->get_cm($assignment->coursemodule);
            $title = resolution_link_helper::get_target($cm);
            $link = (new moodle_url('/course/modedit.php', ['update' => $cm->id]))->out(false);

            $manager = get_grading_manager($cm->context, 'mod_assign', 'submissions');
            if (isset($manager) && $manager->get_active_method() === 'rubric') {
                $controller = $manager->get_active_controller();
                if (!$controller instanceof \gradingform_rubric_controller) {
                    // Moodle falls back to simple grading while the configured rubric is not ready.
                    $message = translation_manager::generate('gradingformunavailable', 'grading');
                    $gradinglink = $manager->get_management_url()->out(false);
                    $check->add_warning($title, $gradinglink, $message);
                    continue;
                }

                if ($assignment->grade < 0) {
                    // It's a scale.
                    $message = translation_manager::generate('rubric_grade_type_scale', 'coursechecker_rubrics_settings');
                    $check->add_failed($title, $link, $message);
                    return;
                }

                $minmax = $controller->get_min_max_score();
                $rubricmax = (float) $minmax['maxscore'];
                $assigngrade = (float) $assignment->grade;
                if ($assigngrade !== $rubricmax) {
                    $message = translation_manager::generate(
                        'rubric_points_mismatch',
                        'coursechecker_rubrics_settings',
                        ['assign_grade' => $assigngrade, 'rubric_max' => $rubricmax]
                    );
                    $check->add_failed($title, $link, $message);
                } else {
                    $message = translation_manager::generate('rubric_points_success', 'coursechecker_rubrics_settings');
                    $check->add_successful($title, $link, $message);
                }
            }
        }
    }
}
