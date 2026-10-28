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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace local_course_checker\output;

use local_course_checker\db\model\check as check_model;
use moodle_url;
use renderable;
use renderer_base;
use stdClass;
use templatable;

/**
 * Templatable and renderable wrapper for a single check, including metadata and rendered results.
 *
 * Prepares check data and rendered results for Mustache output, including status, title, and settings.
 *
 * @package    local_course_checker
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class check implements renderable, templatable {
    /**
     * @var check_model The persistent model for the check.
     */
    protected check_model $check;

    /**
     * @var array Array of rendered result HTML strings grouped by status.
     */
    protected array $renderedresults;

    /**
     * @var bool Whether the check is currently being re-executed.
     */
    protected bool $inprogress;

    /**
     * @var int The course ID associated with this check.
     */
    protected int $courseid;
    /**
     * @var string The version name of the checker.
     */
    private string $versionname;

    /**
     * Constructor.
     *
     * @param check_model $check The persistent check model.
     * @param array $renderedresults Rendered HTML for individual results (grouped by status).
     * @param int $courseid The course ID.
     * @param bool $inprogress Whether this check is currently being run.
     * @param string $versionname The checker version name.
     */
    public function __construct(
        check_model $check,
        array $renderedresults,
        int $courseid,
        bool $inprogress = false,
        string $versionname = 'latest'
    ) {
        $this->check = $check;
        $this->renderedresults = $renderedresults;
        $this->inprogress = $inprogress;
        $this->courseid = $courseid;
        $this->versionname = $versionname;
    }

    /**
     * Export check data for Mustache template rendering.
     *
     * Includes check name, status, timestamp, rendered results, and optionally an action URL if not in progress.
     *
     * @param renderer_base $output The renderer calling this method.
     * @return stdClass Template data object.
     */
    public function export_for_template(renderer_base $output): stdClass {
        // ToDo: Search for avaiable settings, check permissions and then display settings link.
        // ('settings_link' => (new moodle_url('/admin/settings.php',['section' => 'coursechecker_' . $this->check->get('check_name') . '_settings']))->out(false),).

        $data = [
            'status' => $this->check->get('status'),
            'check_name' => $this->check->get('check_name'),
            'plugin_check_name' => get_string('pluginname', 'coursechecker_' . $this->check->get('check_name')),
            'plugin_description' => get_string('pluginname_help', 'coursechecker_' . $this->check->get('check_name')),
            'timestamp' => userdate($this->check->get('timestamp'), '%A, %x %R'),
            'result' => $this->renderedresults,
            'check_is_running' => $this->inprogress,
        ];
        if ($data['status'] === 'running') {
            $data['check_is_running'] = true;
        }
        $data = array_merge($this->check->get('custom_data'), $data);
        if (!$this->inprogress) {
            $data['action_url'] = new moodle_url('/local/course_checker/action.php');
            $data['courseid'] = $this->courseid;
            $data['sesskey'] = sesskey();
            $data['version_name'] = $this->versionname;
            $data['returnurl'] = new moodle_url('/local/course_checker/index.php', [
                'courseid' => $this->courseid,
                'version_name' => $this->versionname,
            ]);
        }

        return (object) $data;
    }
}
