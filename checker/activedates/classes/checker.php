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
 * Checking if all dates are disabled. In reference courses no dates should be enabled.
 *
 * @package    coursechecker_activedates
 * @copyright  2025 Simon Gisler, Fernfachhochschule Schweiz (FFHS) <simon.gisler@ffhs.ch>
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @copyright  based on work by 2020 Christoph Karlen, Fernfachhochschule Schweiz (FFHS) <christoph.karlen@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace coursechecker_activedates;

use cm_info;
use coding_exception;
use dml_exception;
use local_course_checker\db\model\check;
use local_course_checker\mod_type_interface;
use local_course_checker\model\admin_setting_courseregex_trait;
use local_course_checker\model\check_plugin_interface;
use local_course_checker\model\checker_config_trait;
use local_course_checker\resolution_link_helper;
use local_course_checker\translation_manager;
use moodle_exception;
use stdClass;

/**
 * {@inheritDoc}
 */
class checker implements check_plugin_interface, mod_type_interface {
    use checker_config_trait;
    use admin_setting_courseregex_trait;

    /**
     * @var check The result of the check.
     */
    protected check $check;

    /**
     * @var array
     */
    private array $modtypstocheck = [
            self::MOD_TYPE_ASSIGN => [
                    'allowsubmissionsfromdate' => false,
                    'duedate' => false,
                    'cutoffdate' => false,
                    'gradingduedate' => false,
            ],
            self::MOD_TYPE_CHOICE => [
                    'timeopen' => 'choiceopen',
                    'timeclose' => 'choiceclose',
            ],
            self::MOD_TYPE_CHOICEGROUP => [
                    'timeopen' => 'choicegroupopen',
                    'timeclose' => 'choicegroupclose',
            ],
            self::MOD_TYPE_FEEDBACK => [
                    'timeopen' => 'feedbackopen',
                    'timeclose' => 'feedbackclose',
            ],
            self::MOD_TYPE_QUESTIONNAIRE => [
                    'opendate' => false,
                    'closedate' => false,
            ],
            self::MOD_TYPE_QUIZ => [
                    'timeopen' => 'quizopen',
                    'timeclose' => 'quizclose',
            ],
            self::MOD_TYPE_LESSON => [
                    'available' => false,
                    'deadline' => false,
            ],
            self::MOD_TYPE_DATA => [
                    'timeavailablefrom' => 'availablefromdate',
                    'timeavailableto' => 'availabletodate',
                    'timeviewfrom' => 'viewfromdate',
                    'timeviewto' => 'viewtodate',
            ],
            self::MOD_TYPE_FORUM => [
                    'duedate' => false,
                    'cutoffdate' => false,
            ],
            self::MOD_TYPE_SCORM => [
                    'timeopen' => false,
                    'timeclose' => false,
            ],
            self::MOD_TYPE_WORKSHOP => [
                    'submissionstart' => false,
                    'submissionend' => false,
                    'assessmentstart' => false,
                    'assessmentend' => false,
            ],
    ];


    /**
     * Runs the checker.
     *
     * @param stdClass $course
     * @param check $check
     * @return void
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function run(stdClass $course, check $check): void {
        $this->check = $check;

        if (!$this->is_course_check_required('coursechecker_activedates/coursesregex', $course)) {
            $title = translation_manager::generate('admin_setting_coursesregex_skip_course', 'local_course_checker');
            $message = translation_manager::generate('admin_setting_coursesregex_skip_course_desc', 'local_course_checker');
            $this->check->add_successful($title, '', $message);
            return;
        }

        // Get modules from checker setting that are allowed.
        $enabledmodules = explode(',', get_config('coursechecker_activedates', 'modules'));
        // Get all activities for the course.
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

            // Search for problems in the "Activity completion" section.
            if ($cm->completionexpected !== 0) {
                $title = resolution_link_helper::get_target($cm);
                $link = resolution_link_helper::get_link_to_modedit_or_view_page($cm->modname, $cm->id);
                $message = translation_manager::generate('activedates_noactivedates', 'coursechecker_activedates');
                $this->check->set('status', 'failed');
                $this->check->add_failed($title, $link, $message);
            }

            // Search for custom date fields in different activities.
            foreach ($this->modtypstocheck as $modtypekey => $fields) {
                $this->check_mod_date_fields(
                    $cm,
                    $modtypekey,
                    $fields
                );
            }
        }
    }

    /**
     *
     * Checks Modules.
     *
     * @param cm_info $cm
     * @param string $modtype
     * @param array $fields
     * @param bool|null $table
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    private function check_mod_date_fields(cm_info $cm, string $modtype, array $fields, ?bool $table = false): void {
        global $DB;
        $adateissetin = [];

        // We only want to test some modules.
        if ($cm->modname != $modtype) {
            return;
        }

        // Usually base table names of a module corresponds to the modname.
        if (!$table) {
            $table = $modtype;
        }

        $coursemodule = $DB->get_record($table, ['id' => $cm->instance], implode(',', array_keys($fields)));
        foreach ($fields as $field => $languagekey) {
            if ($coursemodule->$field != 0) {
                $adateissetin[] = self::get_field_translation($field, $languagekey, $modtype);
            }
        }

        $title = resolution_link_helper::get_target($cm);
        $link = resolution_link_helper::get_link_to_modedit_or_view_page($cm->modname, $cm->id);

        if (!empty($adateissetin)) {
            $message = translation_manager::generate(
                "activedates_noactivedatesinactivity",
                "coursechecker_activedates",
                [
                    'modtype' => $modtype,
                    'adateissetin' => implode(', ', $adateissetin),
                ]
            );
            $this->check->set('status', 'failed');
            $this->check->add_failed($title, $link, $message);
        } else {
            $message = translation_manager::generate('activedates_success', 'coursechecker_activedates', $modtype);
            $this->check->add_successful($title, $link, $message);
        }
    }

    /**
     * Get the customfield string from Moodle core.
     *  Most times the lang string identifier and the db field name are the same.
     *
     * @param string $field
     * @param string $languagekey
     * @param string|null $modtype
     * @return string
     */
    private static function get_field_translation(string $field, string $languagekey, ?string $modtype = ''): string {
        if ($languagekey) {
            return translation_manager::generate($languagekey, $modtype);
        }
        return translation_manager::generate($field, $modtype);
    }
}
