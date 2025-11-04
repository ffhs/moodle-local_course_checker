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
                'block_course_checker/checker_referencesettings_checklist' => 'checker_referencesettings/checklist',
                'block_course_checker/checker_referencesettings_filter' => 'checker_referencesettings/filter',
                'block_course_checker/checker_referencesettings_formatoptions' => 'checker_referencesettings/formatoptions',
                'block_course_checker/blocks' => 'checker_blocks/blocks',
                'block_course_checker/userdata_modules' => 'checker_userdata/userdata_modules',
                'block_course_checker/checker_links_connect_timeout' => 'checker_links/connect_timeout',
                'block_course_checker/checker_links_timeout' => 'checker_links/timeout',
                'block_course_checker/checker_links_whitelist' => 'checker_links/domain_whitelist',
                'block_course_checker/activedates_modules' => 'checker_activedates/modules',
                'block_course_checker/checker_subheadings_whitelist' => 'checker_subheadings/whitelist',
        ];
        $this->disabled = [
                'block_course_checker/checker_referencesettings_status' => 'checker_referencesettings/disabled',
                'block_course_checker/checker_quiz_status' => 'checker_quiz/disabled',
                'block_course_checker/checker_data_status' => 'checker_data/disabled',
                'block_course_checker/checker_blocks_status' => 'checker_blocks/disabled',
                'block_course_checker/checker_userdata_status' => 'checker_blocks/disabled',
                'block_course_checker/checker_links_status' => 'checker_links/disabled',
                'block_course_checker/checker_groups_status' => 'checker_groups/disabled',
                'block_course_checker/checker_activedates_status' => 'checker_activedates/disabled',
                'block_course_checker/checker_subheadings_status' => 'checker_subheadings/disabled',
                'block_course_checker/checker_attendance_status' => 'checker_attendance/disabled',
        ];
        $this->courseregex = [
                'block_course_checker/checker_activedates_coursesregex' => 'checker_activedates/coursesregex',
                'block_course_checker/checker_userdata_coursesregex' => 'checker_userdata/userdata_coursesregex',
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
        if (in_array('course_checker', $pluginmanager->get_installed_plugins('block'))) {
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
        \core\notification::info('Settings Import finished.');
        return true;
    }
}
