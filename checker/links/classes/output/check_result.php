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

namespace coursechecker_links\output;

use local_course_checker\output\check_result as local_check_result;
use renderer_base;
use stdClass;

/**
 * Templatable and renderable wrapper for a single check result entry.
 *
 * Converts a persistent check result record into Mustache-ready data, including icon and status formatting.
 *
 * @package    coursechecker_links
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class check_result extends local_check_result {
    /**
     * Prepare the check result data for use in a Mustache template.
     *
     * Translates title/message if necessary, resolves status-based styling and icon.
     *
     * @param renderer_base $output Moodle renderer used for generating the status icon.
     * @return stdClass Template-compatible object with formatted check result data.
     */
    public function export_for_template(renderer_base $output) {
        $data = parent::export_for_template($output);
        if (isset($data->filetype)) {
            switch ($data->filetype) {
                default:
                case "unknown":
                    $data->filetypeicon =
                        $output->pix_icon('e/question', get_string('filetype_unknown', 'coursechecker_links'), 'core');
                    break;
                case "html":
                    $data->filetypeicon =
                        $output->pix_icon('e/source_code', get_string('filetype_html', 'coursechecker_links'), 'core');
                    break;
                case "pdf":
                    $data->filetypeicon = $output->pix_icon('f/pdf', get_string('filetype_pdf', 'coursechecker_links'));
                    break;
                case "zip":
                    $data->filetypeicon =
                        $output->pix_icon('asset', get_string('filetype_zip', 'coursechecker_links'), 'mod_scorm');
                    break;
                case "image":
                    $data->filetypeicon =
                        $output->pix_icon('e/insert_edit_image', get_string('filetype_image', 'coursechecker_links'), 'core');
                    break;
            }
        }
        return $data;
    }
}
