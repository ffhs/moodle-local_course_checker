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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin upgrade steps are defined here.
 *
 * @package     local_course_checker
 * @category    upgrade
 * @copyright   2024 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Execute local_course_checker upgrade from the given old version.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_local_course_checker_upgrade($oldversion) {
    if ($oldversion < 2025061000) {
        // Register new capabilities.
        update_capabilities('course_checker');
        upgrade_plugin_savepoint(true, 2025061000, 'local', 'course_checker');
    }
    return true;
}
