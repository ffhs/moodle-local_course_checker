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
 * Admin setting that allows a user to pick blocks for something.
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

/**
 * Class admin_setting_pickblocks
 *
 * Defines an admin setting for selecting blocks using a multi-checkbox interface.
 * This setting extends {@see admin_setting_configmulticheckbox} and dynamically loads available blocks.
 */
class admin_setting_pickblocks extends admin_setting_configmulticheckbox {
    /**
     * @var array Array of available blocks
     */
    private array $blocks;

    /**
     * Constructor for admin_setting_pickblocks.
     *
     * Initializes the setting with a name, visible name, description, and available blocks.
     *
     * @param string $name The name of the config variable.
     * @param string $visiblename The display name of the setting.
     * @param string $description The description of the setting.
     * @param array $blocks Associative array of blocks (`$value => $label`) that will be enabled by default.
     */
    public function __construct($name, $visiblename, $description, $blocks) {
        parent::__construct($name, $visiblename, $description, null, null);
        $this->blocks = $blocks;
    }

    /**
     * Loads available blocks as choices.
     *
     * Retrieves enabled blocks and populates the setting choices.
     *
     * @return bool True if blocks are loaded successfully, false otherwise.
     * @throws coding_exception If an error occurs during processing.
     */
    public function load_choices() {
        if (during_initial_install()) {
            return false;
        }

        if (is_array($this->choices)) {
            return true;
        }

        $blocks = core_plugin_manager::instance()->get_enabled_plugins('block');

        foreach ($blocks as $block) {
            $blocks[$block] = get_string('pluginname', 'block_' . $block);
        }

        if ($blocks) {
            $this->choices = $blocks;
            return true;
        }

        return false;
    }
}
