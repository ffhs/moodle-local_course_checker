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
 * Handles the import of FFHS specific settings.
 *
 * @package    local_course_checker
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_course_checker;

use core_plugin_manager;
use dml_exception;

/**
 * Class import_settings
 *
 * Handles the migration of settings from the old course checker plugin to the new structure.
 * This class ensures that settings are properly transferred and outdated settings are removed.
 *
 * @package    local_course_checker
 */
class import_settings {
    /** @var array Mapping of old settings to new settings. */
    protected array $disabled;
    /** @var array Mapping of settings that contain regex. */
    protected array $courseregex;
    /** @var array Mapping of settings to be imported. */
    protected array $settings;

    /**
     * Initializes the settings and disabled mappings.
     */
    public function init_arrays(): void {
        $this->settings = [
                'block_course_checker/referencecourseid' => 'local_course_checker/referencecourseid',
                'block_course_checker/checker_referencesettings_checklist' => 'coursechecker_referencesettings/checklist',
                'block_course_checker/checker_referencesettings_filter' => 'coursechecker_referencesettings/filter',
                'block_course_checker/checker_referencesettings_formatoptions' => 'coursechecker_referencesettings/formatoptions',
                'block_course_checker/blocks' => 'coursechecker_blocks/blocks',
                'block_course_checker/userdata_modules' => 'coursechecker_userdata/userdata_modules',
                'block_course_checker/checker_links_connect_timeout' => 'coursechecker_links/connect_timeout',
                'block_course_checker/checker_links_timeout' => 'coursechecker_links/timeout',
                'block_course_checker/checker_links_whitelist' => 'coursechecker_links/domain_whitelist',
                'block_course_checker/activedates_modules' => 'coursechecker_activedates/modules',
                'block_course_checker/checker_subheadings_whitelist' => 'coursechecker_subheadings/whitelist',
        ];
        $this->disabled = [
                'block_course_checker/checker_referencesettings_status' => 'coursechecker_referencesettings/disabled',
                'block_course_checker/checker_quiz_status' => 'coursechecker_quiz/disabled',
                'block_course_checker/checker_data_status' => 'coursechecker_data/disabled',
                'block_course_checker/checker_blocks_status' => 'coursechecker_blocks/disabled',
                'block_course_checker/checker_userdata_status' => 'coursechecker_blocks/disabled',
                'block_course_checker/checker_links_status' => 'coursechecker_links/disabled',
                'block_course_checker/checker_groups_status' => 'coursechecker_groups/disabled',
                'block_course_checker/checker_activedates_status' => 'coursechecker_activedates/disabled',
                'block_course_checker/checker_subheadings_status' => 'coursechecker_subheadings/disabled',
                'block_course_checker/checker_attendance_status' => 'coursechecker_attendance/disabled',
        ];
        $this->courseregex = [
                'block_course_checker/checker_activedates_coursesregex' => 'coursechecker_activedates/coursesregex',
                'block_course_checker/checker_userdata_coursesregex' => 'coursechecker_userdata/userdata_coursesregex',
        ];
    }

    /**
     * Starts the import process of settings.
     *
     * @return bool Returns false if the old plugin is not installed, otherwise true after importing settings.
     * @throws dml_exception
     */
    public function start(): bool {
        // Check if the old plugin is installed.
        $pluginmanager = core_plugin_manager::instance();
        if (!array_key_exists('course_checker', $pluginmanager->get_installed_plugins('block'))) {
            \core\notification::info('Block Plugin not found. Settings Import stopped.');
            return false; // Exit if the plugin is not installed.
        }

        \core\notification::info('Block Plugin found. Settings Import started.');
        $this->init_arrays();

        foreach ($this->settings as $old => $new) {
            // Split old plugin/setting.
            [$oldplugin, $oldsetting] = explode('/', $old, 2);
            [$newplugin, $newsetting] = explode('/', $new, 2);

            // Get the old setting value.
            $oldvalue = get_config($oldplugin, $oldsetting);
            if (!empty($oldvalue)) {
                // Set the new value.
                set_config($newsetting, $oldvalue, $newplugin);
            }
        }

        foreach ($this->disabled as $old => $new) {
            // Split old plugin/setting.
            [$oldplugin, $oldsetting] = explode('/', $old, 2);
            [$newplugin, $newsetting] = explode('/', $new, 2);

            // Get the old setting value.
            $oldvalue = get_config($oldplugin, $oldsetting);
            if (empty($oldvalue)) {
                // Set the new value.
                set_config($newsetting, 1, $newplugin);
            }
        }

        foreach ($this->courseregex as $old => $new) {
            // Split old plugin/setting.
            [$oldplugin, $oldsetting] = explode('/', $old, 2);
            [$newplugin, $newsetting] = explode('/', $new, 2);

            // Get the old setting value.
            $oldvalue = str_replace('#', '', get_config($oldplugin, $oldsetting));
            if (!empty($oldvalue)) {
                $cleanedvalue = '/' . trim($oldvalue, '/') . '/';
                // Set the new value.
                set_config($newsetting, $cleanedvalue, $newplugin);
            }
        }
        \core\notification::info('Settings imported successfully. You can now safely uninstall "block_course_checker".');
        return true;
    }
}
