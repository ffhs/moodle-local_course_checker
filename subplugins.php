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
 * Plugin administration pages are defined here.
 * The URL is used to perform all actions when JS is not available.
 * See local/course_checker/classes/table/plugin_management_table.php
 *
 * @package     local_course_checker
 * @category    admin
 * @copyright   2024 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @copyright   2024 Simon Gisler, Fernfachhochschule Schweiz (FFHS) <simon.gisler@ffhs.ch>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(dirname(__FILE__, 3) . '/config.php');

require_once("{$CFG->libdir}/adminlib.php");

$action = optional_param('action', '', PARAM_ALPHA);
$plugin = optional_param('plugin', '', PARAM_PLUGIN);

$PAGE->set_context(context_system::instance());

$PAGE->set_url('/local/course_checker/subplugins.php');

require_login();
require_capability('moodle/site:config', context_system::instance());
require_sesskey();

$coursecheckermanager = \core_plugin_manager::resolve_plugininfo_class('coursechecker');
$pluginname = get_string('pluginname', "coursechecker_{$plugin}");

switch ($action) {
    case 'disable':
        if ($coursecheckermanager::enable_plugin($plugin, 0)) {
            \core\notification::add(
                get_string('plugin_disabled', 'local_course_checker', $pluginname),
                \core\notification::SUCCESS
            );
        }
        break;
    case 'enable':
        if ($coursecheckermanager::enable_plugin($plugin, 1)) {
            \core\notification::add(
                get_string('plugin_enabled', 'local_course_checker', $pluginname),
                \core\notification::SUCCESS
            );
        }
        break;
    default:
}

redirect(new moodle_url('/admin/category.php', [
        'category' => 'local_course_checker',
]));
