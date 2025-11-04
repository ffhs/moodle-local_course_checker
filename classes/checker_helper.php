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
 * This is a helper for the checkers.
 *
 * @package    local_course_checker
 * @copyright  2025 Simon Gisler, Fernfachhochschule Schweiz (FFHS) <simon.gisler@ffhs.ch>
 * @copyright  based on work by 2020 Adrian Perez, Fernfachhochschule Schweiz (FFHS) <adrian.perez@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_course_checker;

/**
 * Class checker_helper
 *
 * @package local_course_checker
 */
class checker_helper implements mod_type_interface {
    /** @var array list of modules which be supported by the checker_userdata */
    const array ACTIVITIES_WITH_USER_DATA = [
            self::MOD_TYPE_DATA,
            self::MOD_TYPE_FORUM,
            self::MOD_TYPE_GLOSSARY,
        // ToDo: "self::MOD_TYPE_JOURNAL" will be implemented later.
            self::MOD_TYPE_WIKI,
        // ToDo: "self::MOD_TYPE_WORKSHOP" will be implemented later.
    ];

    /**
     * Retrieves only supported mods with reset methods for user data.
     * - Copied partially from core -> course/reset_form.php
     *
     * @param array $modnames
     * @return array
     */
    public static function get_userdata_supported_mods(array $modnames): array {
        global $CFG;

        $supportedmods = [];
        foreach ($modnames as $modname) {
            if (!in_array($modname, self::ACTIVITIES_WITH_USER_DATA)) {
                continue;
            }

            $modfile = $CFG->dirroot . '/mod/' . $modname . '/lib.php';
            if (!file_exists($modfile)) {
                continue;
            }

            include_once($modfile);
            $modresetuserdata = $modname . '_reset_userdata';
            if (function_exists($modresetuserdata)) {
                $supportedmods[] = $modname;
            }
        }
        return $supportedmods;
    }
}
