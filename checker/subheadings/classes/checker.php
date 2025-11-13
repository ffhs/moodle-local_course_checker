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

namespace coursechecker_subheadings;

use local_course_checker\translation_manager;
use local_course_checker\model\check_plugin_interface;
use local_course_checker\db\model\check;
use local_course_checker\model\checker_config_trait;
use local_course_checker\resolution_link_helper;

/**
 * Checking the labels subheadings and the leading icons inside the course
 *
 * @package    coursechecker_subheadings
 * @copyright  2025 Simon Gisler, Fernfachhochschule Schweiz (FFHS) <simon.gisler@ffhs.ch>
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @copyright  based on work by 2019 Liip SA <elearning@liip.ch>
 * @copyright  based on work by 2019 Christoph Karlen, Fernfachhochschule Schweiz (FFHS) <christoph.karlen@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class checker implements check_plugin_interface {
    use checker_config_trait;

    /** @var \local_course_checker\db\model\check|null The check object associated with this checker. */
    protected $check = null;
    /** @var array list of ignored strings build from checker settings */
    protected $ignoredstrings;
    /** Module name for labels in Moodle. */
    const MOD_TYPE_LABEL = 'label';
    /** HTML tag expected for the first heading element in label content. */
    const FIRST_ITEM_HTML_TAG = 'h4';
    /** Config setting path for whitelisted subheading content. */
    const WHITELIST_SETTING = 'coursechecker_subheadings/whitelist';
    /** Config setting path for whitelisted heading content. */
    const WHITELIST_HEADING = 'coursechecker_subheadings/whitelist_heading';
    /** Default value for the whitelist config settings. */
    const WHITELIST_DEFAULT = '';
    /**
     * Initialize checker by setting it up with the configuration
     */
    public function init() {
        // Load settings.
        $whitelist = (string) $this->get_config(self::WHITELIST_SETTING, self::WHITELIST_DEFAULT);
        $this->ignoredstrings = array_filter(array_map('trim', explode("\n", $whitelist)));
    }

    /**
     * Runs the checker.
     *
     * @param \stdClass $course
     * @param check $check
     * @return void
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public function run(\stdClass $course, check $check): void {
        $this->init();
        // Initialize check result array.
        $this->check = new check();
        // Get all labels activities for the course.
        $modinfo = get_fast_modinfo($course);
        // Get a dom document for html operations.
        $dom = new \DOMDocument();
        foreach ($modinfo->cms as $cm) {
            // Skip activities that are not labels.
            if ($cm->modname != self::MOD_TYPE_LABEL) {
                continue;
            }
            // Skip activities that are not visible.
            if (!$cm->uservisible) {
                continue;
            }
            // Link to activity.
            $title = resolution_link_helper::get_target($cm);
            $link = resolution_link_helper::get_link_to_modedit_or_view_page($cm->modname, $cm->id);
            // Load the html content
            // - DOMDocument is not loading correctly if there are line breaks.
            $cmcontentwithoutnewlines = preg_replace("/[\r\n]/", '', $cm->content);
            $dom->loadHTML($cmcontentwithoutnewlines);
            $body = $dom->getElementsByTagName('body');
            if (!is_object($body)) {
                $this->add_general_error($title, $link);
                continue;
            }
            try {
                $elements = $body
                    ->item(0)->childNodes
                    ->item(0)->childNodes;
                $firstitem = $elements->item(0);
            } catch (\Exception $exception) {
                $this->add_general_error($title, $link);
                continue;
            }
            // Check if the text contains strings which are whitelisted.
            foreach ($this->ignoredstrings as $ignoredstring) {
                $pos = strpos($cmcontentwithoutnewlines, $ignoredstring);
                if ($pos !== false) {
                    $message = translation_manager::generate("subheadings_labelignored", "coursechecker_subheadings");
                    $this->check->add_successful($link, $title, $message);
                    continue 2;
                }
            }
            // Check if the first html element is set and has a correct header.
            if (!isset($firstitem->tagName) || $firstitem->tagName != self::FIRST_ITEM_HTML_TAG) {
                $message = translation_manager::generate(
                    "subheadings_wrongfirsthtmltag",
                    "coursechecker_subheadings",
                    (object) ["htmltag" => self::FIRST_ITEM_HTML_TAG]
                );
                $this->check->add_failed($link, $title, $message);
                $this->check->set('status', 'failed');
                continue;
            }
            // Check if there is an icon in the first heading.
            $search = "(\[((?:icon\s)?fa-[a-z0-9 -]+)\])is";
            preg_match($search, $firstitem->textContent, $matches);
            if (empty($matches)) {
                $message = translation_manager::generate("subheadings_iconmissing", "coursechecker_subheadings");
                $this->check->add_failed($link, $title, $message);
                $this->check->set('status', 'failed');
                continue;
            }
            // When there are no problems.
            $message = translation_manager::generate('subheadings_success', 'coursechecker_subheadings');
            $this->check->add_successful($link, $title, $message);
        }
    }

    /**
     * Adds a general failure result to the current check with a translated message.
     *
     * This marks the check as failed and associates it with a specific title and link
     * that describe the issue.
     *
     * @param string $title The title describing the failed result.
     * @param string $link The URL related to the failed check.
     * @throws \coding_exception If the translation string is missing or invalid.
     */
    private function add_general_error($title, $link) {
        $message = translation_manager::generate("subheadings_generalerror", "coursechecker_subheadings");
        $this->check->add_failed($link, $title, $message);
        $this->check->set('status', 'failed');
    }
}
