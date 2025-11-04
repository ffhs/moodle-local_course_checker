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

use local_course_checker\db\model\check_result as check_result_model;
use local_course_checker\translation_manager;
use renderable;
use renderer_base;
use stdClass;
use templatable;

/**
 * Templatable and renderable wrapper for a single check result entry.
 *
 * Converts a persistent check result record into Mustache-ready data, including icon and status formatting.
 *
 * @package    local_course_checker
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class check_result implements renderable, templatable {
    /**
     * @var check_result_model The persistent model for the individual check result.
     */
    private check_result_model $checkresult;

    /**
     * check_result constructor.
     *
     * @param check_result_model $checkresult The persistent check result instance.
     */
    public function __construct(check_result_model $checkresult) {
        $this->checkresult = $checkresult;
    }

    /**
     * Prepare the check result data for use in a Mustache template.
     *
     * Translates title/message if necessary, resolves status-based styling and icon.
     *
     * @param renderer_base $output Moodle renderer used for generating the status icon.
     * @return stdClass Template-compatible object with formatted check result data.
     */
    public function export_for_template(renderer_base $output) {
        $data = [
            'title' => translation_manager::resolve($this->checkresult->get('title')),
            'link' => $this->checkresult->get('link'),
            'message' => translation_manager::resolve($this->checkresult->get('message')),
            'timestamp' => userdate($this->checkresult->get('timestamp'), '%A, %x %R'),
        ];

        $data['link_checker_additional'] = $this->get_link_checker_additional();

        switch ($this->checkresult->get('status')) {
            default:
            case "error":
                $data['text_class'] = 'text-danger';
                $data['icon'] = $output->pix_icon('req', 'error', 'core');
                break;
            case "failed":
                $data['text_class'] = 'text-danger';
                $data['icon'] = $output->pix_icon('f/error', 'failed', 'tool_brickfield');
                break;
            case "warning":
                $data['text_class'] = 'text-warning';
                $data['icon'] = $output->pix_icon('partial', 'warning', 'tool_policy');
                break;
            case "successful":
                $data['text_class'] = 'text-success';
                $data['icon'] = $output->pix_icon('f/done', 'successful', 'tool_brickfield');
                break;
        }
        return (object)$data;
    }

    /**
     * Prepare url's for the link checker to use in a Mustache template.
     *
     * @return array Prepared URLs.
     */
    private function get_link_checker_additional(): array {
        $rawmessage = $this->checkresult->get('message');

        if (empty($rawmessage) || !is_string($rawmessage)) {
            return [];
        }

        preg_match_all('/\{.*?\}(?=\s*\{|\s*$)/s', $rawmessage, $matches);

        if (empty($matches[0])) {
            return [];
        }

        $jsonobjects = $matches[0];

        $urls = [];

        foreach ($jsonobjects as $json) {
            $decoded = json_decode($json);
            if (json_last_error() === JSON_ERROR_NONE && isset($decoded->a->url)) {
                $urls[] = $decoded->a->url;
            }
        }
        return $urls;
    }
}
