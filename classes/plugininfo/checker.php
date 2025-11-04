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
 * Subplugin info class.
 *
 * @package    local_course_checker
 * @copyright  2024 Simon Gisler, Fernfachhochschule Schweiz (FFHS)  <simon.gisler@ffhs.ch>
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_course_checker\plugininfo;

defined('MOODLE_INTERNAL') || die();

use coding_exception;
use core\plugininfo\base;
use core_plugin_manager;
use admin_settingpage;
use dml_exception;
use local_course_checker\db\model\check;
use part_of_admin_tree;

/**
 * Class checker
 *
 * Handles enabling, disabling, and managing the lifecycle of checker plugins.
 * This class provides functions to retrieve enabled plugins, manage their state,
 * and integrate them into the Moodle admin settings.
 *
 * @package    local_course_checker
 */
class checker extends base {
    /**
     * Determines whether this plugin type supports disabling plugins.
     *
     * @return bool True if the plugin type supports disabling, false otherwise.
     */
    public static function plugintype_supports_disabling(): bool {
        return true;
    }

    /**
     * Determines whether the plugin can be uninstalled via the administration UI.
     *
     * @return bool True if uninstallation is allowed, false otherwise.
     */
    public function is_uninstall_allowed(): bool {
        return true;
    }


    /**
     * Returns the node name used in the admin settings menu for this plugin's settings.
     *
     * @return null|string The node name or null if the plugin does not create a settings node.
     */
    public function get_settings_section_name(): ?string {
        if (!file_exists($this->full_path('settings.php'))) {
            return null;
        }
        return "checker_{$this->name}_settings";
    }


    /**
     * Retrieves the list of enabled checker plugins.
     *
     * @return array List of enabled plugins.
     * @throws dml_exception
     */
    public static function get_enabled_plugins(): array {
        $pluginmanager = core_plugin_manager::instance();
        $plugins = $pluginmanager->get_installed_plugins('checker');
        if (!$plugins) {
            return [];
        }
        // Filter to return only enabled plugins.
        $enabled = [];
        foreach (array_keys($plugins) as $pluginname) {
            $disabled = get_config('checker_' . $pluginname, 'disabled');
            if (empty($disabled)) {
                $enabled[$pluginname] = $pluginname;
            }
        }
        return $enabled;
    }

    /**
     * Checks whether a specific checker plugin is enabled.
     *
     * @param string $pluginname The name of the plugin.
     * @return bool True if the plugin is enabled, false otherwise.
     * @throws dml_exception
     */
    public static function is_plugin_enabled(string $pluginname): bool {
        $disabled = get_config('checker_' . $pluginname, 'disabled');
        if (empty($disabled)) {
            return true;
        }
        return false;
    }

    /**
     * Enables or disables a checker plugin.
     *
     * @param string $pluginname The name of the plugin.
     * @param int $enabled 1 to enable, 0 to disable.
     * @return bool True if the state was changed, false otherwise.
     * @throws dml_exception
     */
    public static function enable_plugin(string $pluginname, int $enabled): bool {
        $checkerpluginname = 'checker_' . $pluginname;

        $oldvalue = !empty(get_config($checkerpluginname, 'disabled'));
        $disabled = empty($enabled);
        $haschanged = false;

        // Only set value if there is no config setting or if the value is different from the previous one.
        if (!$oldvalue && $disabled) {
            set_config('disabled', true, $checkerpluginname);
            $haschanged = true;
        } else if ($oldvalue && !$disabled) {
            unset_config('disabled', $checkerpluginname);
            $haschanged = true;
        }

        if ($haschanged) {
            add_to_config_log('disabled', $oldvalue, $disabled, $checkerpluginname);
            core_plugin_manager::reset_caches();
        }

        return $haschanged;
    }

    /**
     * Loads the settings for a checker plugin into the Moodle admin UI.
     *
     * @param part_of_admin_tree $adminroot The admin tree structure.
     * @param string $parentnodename The parent node name in the admin UI.
     * @param bool $hassiteconfig Whether the site configuration is accessible.
     * @return void
     * @throws dml_exception
     */
    public function load_settings(part_of_admin_tree $adminroot, $parentnodename, $hassiteconfig): void {

        $ADMIN = $adminroot;

        // Check if the plugin is installed and upgraded.
        if (!$this->is_installed_and_upgraded()) {
            return;
        }

        // Check if the plugin has site configuration capabilities or a settings file.
        if (!$hassiteconfig || !file_exists($this->full_path('settings.php'))) {
            return;
        }

        $section = $this->get_settings_section_name();

        // Check if the plugin is disabled using the `get_config` function.
        $disabled = get_config('checker_' . $this->name, 'disabled');
        if (!empty($disabled)) {
            $settings = new admin_settingpage($section, $this->displayname, 'moodle/site:config', true);
        } else {
            $settings = new admin_settingpage($section, $this->displayname, 'moodle/site:config', false);
        }

        include($this->full_path('settings.php'));
        $ADMIN->add($parentnodename, $settings);
    }

    /**
     * Cleans up data related to the plugin upon uninstallation.
     *
     * @return void
     * @throws coding_exception
     */
    public function uninstall_cleanup() {
        check::delete_by_check_name($this->name);
        parent::uninstall_cleanup();
    }
}
