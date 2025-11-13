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
 *
 * @package     local_course_checker
 * @category    admin
 * @copyright   2024 Simon Gisler, Fernfachhochschule Schweiz (FFHS) <simon.gisler@ffhs.ch>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_course_checker\table;

use core\exception\moodle_exception;
use moodle_url;
use help_icon;
use html_writer;
use stdClass;

/**
 * {@inheritDoc}
 */
class plugin_management_table extends \core_admin\table\plugin_management_table {
    /**
     * {@inheritDoc}
     */
    protected function get_plugintype(): string {
        return 'coursechecker';
    }

    /**
     * {@inheritDoc}
     */
    public function guess_base_url(): void {
        $this->define_baseurl(
            new moodle_url('/admin/settings.php', ['section' => 'local_course_checker_settings'])
        );
    }

    /**
     * Get the action URL for this table.
     *
     * The action URL is used to perform all actions when JS is not available.
     *
     * @param array $params
     * @return moodle_url
     * @throws moodle_exception
     */
    protected function get_action_url(array $params = []): moodle_url {
        return new moodle_url('/local/course_checker/subplugins.php', $params);
    }

    /**
     * Show the name column content with a help icon to describe the plugin.
     *
     * @param stdClass $row
     * @return string
     */
    protected function col_name(stdClass $row): string {
        global $OUTPUT;
        $status = $row->plugininfo->get_status();
        if ($status === \core_plugin_manager::PLUGIN_STATUS_MISSING) {
            return html_writer::span(
                get_string('pluginmissingfromdisk', 'core', $row->plugininfo),
                'notifyproblem'
            );
        }

        $icon = new help_icon('pluginname', $row->plugin);
        $name = $row->plugininfo->displayname . ' ' . $OUTPUT->render($icon);

        if ($row->plugininfo->is_installed_and_upgraded()) {
            return $name;
        }

        return html_writer::span(
            $name,
            'notifyproblem'
        );
    }
}
