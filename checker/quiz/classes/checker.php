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
 * Checking quizzes inside the course if the sumgrade matches the grade.
 *
 * @package    coursechecker_quiz
 * @copyright  2025 Simon Gisler, Fernfachhochschule Schweiz (FFHS) <simon.gisler@ffhs.ch>
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @copyright  based on work by 2020 Christoph Karlen, Fernfachhochschule Schweiz (FFHS) <christoph.karlen@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace coursechecker_quiz;

use coding_exception;
use local_course_checker\config;
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
     * Defines the path to the setting regex_whitelist.
     */
    const WHITELISTREGEX = 'coursechecker_quiz/regex_whitelist';
    /**
     * Defines the default of the setting regex_whitelist.
     */
    const WHITELISTREGEX_DEFAULT = "/Aufgaben zur Prüfungsvorbereitung/i";
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
     * @throws \core\exception\moodle_exception
     * @throws \moodle_exception
     * @throws coding_exception
     */
    public function run(stdClass $course, check $check): void {
        $this->check = $check;

        // Get all quiz activities in the course.
        $modinfo = get_fast_modinfo($course);
        $instances = get_all_instances_in_courses(self::MOD_TYPE_QUIZ, [$course->id => $course]);
        $regex = config::get(self::WHITELISTREGEX, self::WHITELISTREGEX_DEFAULT);
        foreach ($instances as $mod) {
            // Get cm_info object to use for target and resolution link.
            $cm = $modinfo->get_cm($mod->coursemodule);
            $title = resolution_link_helper::get_target($cm);
            $resolutionlink = (new moodle_url('/mod/quiz/edit.php', ['cmid' => $cm->id]))->out(false);
            // For all quizzes we like to check if the "Maximum grade" and the "Total of marks" are the same numbers.
            if ($regex && preg_match($regex, $mod->name)) {
                $this->check->add_info(
                    $title,
                    $resolutionlink,
                    translation_manager::generate('quiz_in_whitelist', 'coursechecker_quiz')
                );
            } else {
                $this->check_quiz_maximum_grade($mod, $resolutionlink, $title);
            }
        }
    }

    /**
     * Check quiz maximum grade.
     *
     * @param stdClass $mod
     * @param string $link
     * @param string|null $title
     * @throws coding_exception
     */
    protected function check_quiz_maximum_grade(stdClass $mod, string $link = '', ?string $title = null): void {
        if ($mod->grade != $mod->sumgrades) {
            $message = translation_manager::generate(
                'quiz_grade_sum_error',
                'coursechecker_quiz',
                [
                    'grade' => $mod->grade,
                    'sumgrades' => $mod->sumgrades,
                ]
            );
            $this->check->add_failed($title, $link, $message);
        } else {
            $message = translation_manager::generate(
                'quiz_grade_sum_success',
                'coursechecker_quiz'
            );
            $this->check->add_successful($title, $link, $message);
        }
    }
}
