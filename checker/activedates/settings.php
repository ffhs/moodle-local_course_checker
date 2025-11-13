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
 * Settings for checking active dates of activities inside the course
 *
 * @package    coursechecker_activedates
 * @copyright  2025 Simon Gisler, Fernfachhochschule Schweiz (FFHS) <simon.gisler@ffhs.ch>
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @copyright  based on work by 2019 Liip SA <elearning@liip.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_course_checker\admin\admin_setting_courseregex;
use local_course_checker\admin\admin_setting_pickmodules;

defined('MOODLE_INTERNAL') || die();

global $CFG;

if ($hassiteconfig) {
    /** @var admin_settingpage $settings */
    $settings;
    $coursesregex = new admin_setting_courseregex('coursechecker_activedates/coursesregex');
    $settings->add($coursesregex);

    $visiblename = get_string('activedates_setting_modules', 'coursechecker_activedates');
    $url = $CFG->wwwroot . '/admin/modules.php';
    $description = get_string('activedates_setting_modules_help', 'coursechecker_activedates', $url);
    $modules = new admin_setting_pickmodules('coursechecker_activedates/modules', $visiblename, $description, []);
    $settings->add($modules);
}
