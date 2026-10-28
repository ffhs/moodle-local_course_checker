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

use core_text;
use local_course_checker\db\model\check_result as check_result_model;
use local_course_checker\translation_manager;
use Random\RandomException;
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
    protected check_result_model $checkresult;

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
        // Mustache escapes {{title}} after the optional Easter egg is applied.
        $title = translation_manager::resolve($this->checkresult->get('title'), false);
        try {
            // 0.5% Chance to add a Penguin inside the title.
            // The penguin disappears on reload. No one is going to believe them. :)
            if (random_int(1, 200) === 1) {
                $spacepositions = [];
                $pos = core_text::strpos($title, ' ');
                while ($pos !== false) {
                    $spacepositions[] = $pos;
                    $pos = core_text::strpos($title, ' ', $pos + 1);
                }
                // Replace a random space with a penguin.
                if (!empty($spacepositions)) {
                    $chosenspace = $spacepositions[array_rand($spacepositions)];
                    $title = core_text::substr($title, 0, $chosenspace) . '🐧' . core_text::substr($title, $chosenspace + 1);
                }
            }
        } catch (RandomException $e) {
            $title = translation_manager::resolve($this->checkresult->get('title'), false);
        }
        $data = [
            'title' => $title,
            'link' => $this->checkresult->get('link'),
            'message' => translation_manager::resolve($this->checkresult->get('message')),
            'timestamp' => userdate($this->checkresult->get('timestamp'), '%A, %x %R'),
        ];
        $data = array_merge($this->checkresult->get('custom_data'), $data);

        switch ($this->checkresult->get('status')) {
            default:
            case "error":
                $data['text_class'] = 'text-danger';
                $data['icon'] = $output->pix_icon('req', get_string('error_checks', 'local_course_checker'), 'core');
                break;
            case "failed":
                $data['text_class'] = 'text-danger';
                $data['icon'] =
                    $output->pix_icon('f/error', get_string('failed_checks', 'local_course_checker'), 'tool_brickfield');
                break;
            case "warning":
                $data['text_class'] = 'text-warning';
                $data['icon'] = $output->pix_icon('partial', get_string('warning_checks', 'local_course_checker'), 'tool_policy');
                break;
            case "info":
                $data['text_class'] = 'local_course_checker__check-info-color';
                $data['icon'] = $output->pix_icon('e/cell_props', get_string('info_checks', 'local_course_checker'), 'core');
                break;
            case "successful":
                $data['text_class'] = 'text-success';
                $data['icon'] =
                    $output->pix_icon('f/done', get_string('successful_checks', 'local_course_checker'), 'tool_brickfield');
                break;
        }
        return (object) $data;
    }
}
