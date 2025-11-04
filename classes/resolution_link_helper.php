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
 * resolution_link_helper provides utility methods to generate Moodle URLs
 * to various course-related pages like module settings, course view/edit, etc.
 *
 * @package    local_course_checker
 * @copyright  2025 Simon Gisler, Fernfachhochschule Schweiz (FFHS) <simon.gisler@ffhs.ch>
 * @copyright  based on work by 2020 Christoph Karlen, Fernfachhochschule Schweiz (FFHS) <christoph.karlen@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_course_checker;

use cm_info;
use coding_exception;
use context;
use moodle_url;
use stdClass;

/**
 * Class resolution_link_helper
 *
 * @package    local_course_checker
 */
class resolution_link_helper implements mod_type_interface {
    /** @var array list of modules which can be linked directly to the module config page */
    const DIRECT_MOD_NAMES = [
            self::MOD_TYPE_ASSIGN,
            self::MOD_TYPE_BOOK,
            self::MOD_TYPE_CHOICE,
            self::MOD_TYPE_CHOICEGROUP,
            self::MOD_TYPE_DATA,
            self::MOD_TYPE_FEEDBACK,
            self::MOD_TYPE_FORUM,
            self::MOD_TYPE_LABEL,
            self::MOD_TYPE_LESSON,
            self::MOD_TYPE_PAGE,
            self::MOD_TYPE_QUESTIONNAIRE,
            self::MOD_TYPE_QUIZ,
            self::MOD_TYPE_RESOURCE,
            self::MOD_TYPE_URL,
            self::MOD_TYPE_WIKI,
    ];

    /**
     * Returns a link to the module's edit or view page depending on the module type.
     *
     * @param string $modname The name of the module (e.g., 'quiz', 'assign').
     * @param int $coursemoduleid The course module ID.
     * @param bool|null $gotoeditsettingspage Whether to link to the edit settings page (true) or the view page (false).
     * @return string The generated URL as a string.
     * @throws coding_exception
     */
    public static function get_link_to_modedit_or_view_page(string $modname, int $coursemoduleid, ?bool $gotoeditsettingspage = true): string {
        // We open the edit settings page instead of the mod/view itself.
        if (in_array($modname, self::DIRECT_MOD_NAMES) && $gotoeditsettingspage) {
            $url = new moodle_url('/course/mod.php', [
                    "update" => $coursemoduleid,
                    "sesskey" => sesskey(),
                    "sr" => 0,
            ]);
        } else {
            $url = new moodle_url('/mod/' . $modname . '/view.php', ['id' => $coursemoduleid]);
        }
        return $url->out(false);
    }

    /**
     * Returns a URL to the course view page.
     *
     * @param int $courseid The course ID.
     * @return string The course view URL.
     */
    public static function get_link_to_course_view_page(int $courseid): string {
        return (new moodle_url('/course/view.php', [
                'id' => $courseid,
        ]))->out(false);
    }

    /**
     * Returns a URL to the course edit page.
     *
     * @param stdClass $course The course object.
     * @return string The course edit URL.
     */
    public static function get_link_to_course_edit_page(stdClass $course): string {
        return (new moodle_url('/course/edit.php', [
                'id' => $course->id,
        ]))->out(false);
    }

    /**
     * Returns a URL to the course group management page.
     *
     * @param stdClass $course The course object.
     * @param stdClass|null $group Optional group object.
     * @return string The group management URL.
     */
    public static function get_link_to_course_group_page(stdClass $course, ?stdClass $group = null): string {
        $urlparam = [
            'id' => $course->id,
        ];
        if (is_object($group)) {
            $urlparam['group'] = $group->id;
        }
        return (new moodle_url('/group/index.php', $urlparam))->out(false);
    }
    /**
     * Returns a URL to the course filter management page.
     *
     * @param context $coursecontext The course context object.
     * @return string The course filter management URL.
     */
    public static function get_link_to_course_filter_page(context $coursecontext): string {
        return (new moodle_url('/filter/manage.php', [
                'contextid' => $coursecontext->id,
        ]))->out(false);
    }

    /**
     * Generates a translated string representing the target of the given course module.
     *
     * @param cm_info $cm The course module info object.
     * @param string $checkername The name of the checker plugin (optional).
     * @return string A translated description of the target.
     */
    public static function get_target(cm_info $cm, string $checkername = ''): string {
        if ($checkername == 'checker_links') {
            $targetcontext = (object) [
                "modname" => translation_manager::generate("pluginname", $cm->modname),
                "name" => strip_tags($cm->name),
            ];
            $target = translation_manager::generate("checker_links_activity", "local_course_checker", $targetcontext);
        } else {
            $targetcontext = (object) ["name" => strip_tags($cm->name)];
            $target = translation_manager::generate("groups_activity", "local_course_checker", $targetcontext);
        }
        return $target;
    }
}
