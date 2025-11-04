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
 * Admin setting that allows a user to pick modules for something.
 *
 * @package    local_course_checker
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @copyright  based on work by 2020 Adrian Perez, Fernfachhochschule Schweiz (FFHS) <adrian.perez@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_course_checker\admin;

use admin_setting_configmulticheckbox;
use coding_exception;
use core_plugin_manager;
use local_course_checker\checker_helper;

/**
 * Class admin_setting_pickmodules
 *
 * Defines an admin setting for selecting modules using a multi-checkbox interface.
 * This setting extends {@see admin_setting_configmulticheckbox} and dynamically loads available modules.
 */
class admin_setting_pickmodules extends admin_setting_configmulticheckbox {
    /**
     * @var array Array of available modules
     */
    private array $modules;

    /**
     * Constructor for admin_setting_pickmodules.
     *
     * Initializes the setting with a name, visible name, description, and available modules.
     *
     * @param string $name The name of the config variable.
     * @param string $visiblename The display name of the setting.
     * @param string $description The description of the setting.
     * @param array $modules Associative array of modules (`$value => $label`) that will be enabled by default.
     */
    public function __construct($name, $visiblename, $description, $modules) {
        parent::__construct($name, $visiblename, $description, null, null);
        $this->modules = $modules;
    }

    /**
     * Loads available modules as choices.
     *
     * Retrieves enabled modules and filters them based on the setting type.
     * If the setting name is `userdata_modules`, only modules that support user data reset are included.
     *
     * @return bool True if modules are loaded successfully, false otherwise.
     * @throws coding_exception If an error occurs during processing.
     */
    public function load_choices() {
        if (during_initial_install()) {
            return false;
        }

        if (is_array($this->choices)) {
            return true;
        }

        $plugins = core_plugin_manager::instance()->get_enabled_plugins('mod');

        // Filter modules that support user data reset, used in checker_userdata.
        if ($this->name === 'userdata_modules') {
            $plugins = checker_helper::get_userdata_supported_mods($plugins);
        }

        $modules = [];
        foreach ($plugins as $plugin) {
            $modules[$plugin] = get_string('modulename', $plugin);
        }

        if ($modules) {
            $this->choices = $modules;
            return true;
        }

        return false;
    }
}
