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
 * Plugin administration pages are defined here.
 *
 * @package     local_course_checker
 * @category    admin
 * @copyright   2024 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_course_checker\admin\admin_setting_courseid_selector;
use local_course_checker\plugininfo\checker;
use local_course_checker\table\plugin_management_table;

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $ADMIN->add('localplugins', new admin_category(
        'local_course_checker',
        get_string('pluginname', 'local_course_checker', null, true)
    ));

    $settings = new admin_settingpage('local_course_checker_settings', get_string('settings_name', 'local_course_checker', null, true));

    if ($ADMIN->fulltree) {
        $settings->add(new admin_setting_heading(
            'local_course_checker_checker/description',
            null,
            get_string('settings_general', 'local_course_checker')
        ));

        // Subplugin Manager.
        $settings->add(new \core_admin\admin\admin_setting_plugin_manager(
            'checker',
            plugin_management_table::class,
            'local_course_checker_settings',
            get_string('settings_name', 'local_course_checker'),
        ));

        // Define reference course id.
        $visiblename = get_string("settings_referencecourseid", "local_course_checker");
        $settings->add(new admin_setting_courseid_selector('local_course_checker/referencecourseid', $visiblename, '', SITEID));
    }
    $ADMIN->add('local_course_checker', $settings);
    unset($settings);

    $checkers = core_plugin_manager::instance()->get_plugins_of_type('checker');

    if (!empty($checkers)) {
        /** @var checker $plugin */
        foreach ($checkers as $plugin) {
            if (method_exists($plugin, 'load_settings')) {
                try {
                    $plugin->load_settings($ADMIN, 'local_course_checker', $hassiteconfig);
                } catch (coding_exception $e) {
                    debugging(sprintf($e));
                }
            } else {
                debugging(sprintf('Subplugin %s does not implement load_settings method', get_class($plugin)));
            }
        }
    }
    $settings = null;
}
