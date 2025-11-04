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

namespace local_course_checker\output;

use renderable;
use renderer_base;
use stdClass;
use templatable;
use local_course_checker\db\model\checker as checker_model;

/**
 * Renderable and templatable class for displaying a checker instance with its checks.
 *
 * Used to pass rendered check sections (grouped by status) to the Mustache template layer.
 *
 * @package    local_course_checker
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class checker implements renderable, templatable {
    /**
     * @var checker_model The persistent checker model containing meta information.
     */
    private checker_model $checker;

    /**
     * @var array The rendered check sections, grouped by status (e.g., error, warning, successful).
     */
    private array $renderedchecks;

    /**
     * Constructor.
     *
     * @param checker_model $checker The checker persistent model instance.
     * @param array $renderedchecks An associative array of rendered checks grouped by status.
     */
    public function __construct(checker_model $checker, array $renderedchecks) {
        $this->checker = $checker;
        $this->renderedchecks = $renderedchecks;
    }

    /**
     * Prepare data for use in a Mustache template.
     *
     * @param renderer_base $output Renderer used for additional formatting or URLs if needed.
     * @return stdClass A standard object containing template variables.
     */
    public function export_for_template(renderer_base $output): stdClass {
        return (object)[
            'version_name' => $this->checker->get('version_name'),
            'timestamp' => userdate($this->checker->get('timestamp'), '%A, %x %R'),
            'checks' => $this->renderedchecks,
        ];
    }
}
