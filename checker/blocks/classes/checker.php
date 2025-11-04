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
 * Checking if blocks are present in a course.
 *
 * @package    checker_blocks
 * @copyright  2025 Simon Gisler, Fernfachhochschule Schweiz (FFHS) <simon.gisler@ffhs.ch>
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @copyright  based on work by 2019 Adrian Perez, Fernfachhochschule Schweiz (FFHS) <adrian.perez@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace checker_blocks;

use dml_exception;
use local_course_checker\model\check_plugin_interface;
use local_course_checker\db\model\check;
use local_course_checker\model\checker_config_trait;
use local_course_checker\resolution_link_helper;
use local_course_checker\translation_manager;
use stdClass;

/**
 * {@inheritDoc}
 */
class checker implements check_plugin_interface {
    use checker_config_trait;

    /**
     * Defines the path to the setting referencecourseid.
     */
    const REFERENCE_COURSE = 'local_course_checker/referencecourseid';
    /**
     * Defines the default of the setting referencecourseid.
     */
    const REFERENCE_COURSE_DEFAULT = 1;

    /** @var int $referencecourseid from checker settings */
    protected int $referencecourseid;

    /** @var array $enabledblocks from checker settings */
    protected array $enabledblocks;

    /**
     * @var check The result of the check.
     */
    protected check $check;

    /**
     * Runs the checker.
     *
     * @param stdClass $course
     * @param check $check
     * @return void
     * @throws \coding_exception
     * @throws dml_exception
     */
    public function run(stdClass $course, check $check): void {
        global $DB;

        // Get active setting checks from configuration.
        $this->init();

        // Initialize check result array.
        $this->check = $check;

        // Set contexts to check.
        $context = $DB->get_record('context', ['instanceid' => $course->id, 'contextlevel' => CONTEXT_COURSE]);
        $refcontext = $DB->get_record(
            'context',
            ['instanceid' => $this->referencecourseid, 'contextlevel' => CONTEXT_COURSE]
        );

        // Loading blocks and instances in the region.
        foreach ($this->enabledblocks as $block) {
            $courseblock = $this->get_block_in_course_by_context($block, $context);

            if (!$refcontext) {
                continue;
            }
            $refblock = $this->get_block_in_course_by_context($block, $refcontext);

            $targetcontext = (object) ["name" => strip_tags($block)];
            $title = translation_manager::generate(
                "blocks_activity",
                "checker_blocks",
                $targetcontext
            );
            $resolutionlink = resolution_link_helper::get_link_to_course_view_page($course->id);

            // What are the differences? (if any).
            $comparison = $this->get_comparison_string($refblock, $courseblock);

            // Skip if no block is present in both contexts.
            if (!$courseblock && !$refblock) {
                continue;
            }

            // When there aren't two blocks and blockname is not equal (for whatever reason - should not).
            if ((!$courseblock || !$refblock) || ($courseblock->blockname != $refblock->blockname)) {
                $message = translation_manager::generate('blocks_error', 'checker_blocks');
                $this->check->add_failed($title, $resolutionlink, $message . ' ' . $comparison);
                $this->check->set('status', 'failed');
                continue;
            }

            $message = translation_manager::generate('blocks_success', 'checker_blocks');
            $this->check->add_successful($title, $resolutionlink, $message);
        }
    }

    /**
     * Initialize checker by setting it up with the configuration
     *
     */
    public function init(): void {
        // Load settings.
        $this->referencecourseid = (int) $this->get_config(
            self::REFERENCE_COURSE,
            self::REFERENCE_COURSE_DEFAULT
        );
        $this->enabledblocks = explode(',', $this->get_config('checker_blocks/blocks'));
    }

    /**
     * Get block instances by blockname and course_context.
     *
     * @param string $block
     * @param stdClass $context
     * @return bool|false|mixed|stdClass
     * @throws dml_exception
     */
    private function get_block_in_course_by_context(string $block, stdClass $context): mixed {
        global $DB;

        return $DB->get_record('block_instances', [
                'blockname' => $block,
                'parentcontextid' => $context->id,
        ]);
    }

    /**
     * Gets the difference.
     *
     * @param mixed $refblock
     * @param mixed $courseblock
     * @return string
     */
    private function get_comparison_string(mixed $refblock, mixed $courseblock): string {
        return translation_manager::generate(
            'blocks_comparison',
            'checker_blocks',
            [
                'valuereference' => ($refblock !== false) ? '1' : '0',
                'valuecurrent' => ($courseblock !== false) ? '1' : '0',
            ]
        );
    }
}
