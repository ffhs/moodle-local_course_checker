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
 * Checking links inside the course
 *
 * @package    coursechecker_links
 * @copyright  2025 Simon Gisler, Fernfachhochschule Schweiz (FFHS) <simon.gisler@ffhs.ch>
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @copyright  based on work by 2019 Liip SA <elearning@liip.ch>
 * @copyright  based on work by 2020 Christoph Karlen, Fernfachhochschule Schweiz (FFHS) <christoph.karlen@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace coursechecker_links;

use coding_exception;
use course_modinfo;
use dml_exception;
use local_course_checker\mod_type_interface;
use local_course_checker\model\check_plugin_interface;
use local_course_checker\db\model\check;
use local_course_checker\resolution_link_helper;
use local_course_checker\translation_manager;
use moodle_exception;
use moodle_url;
use stdClass;

/**
 * {@inheritDoc}
 */
class checker implements check_plugin_interface, mod_type_interface {
    /**
     * @var curl_manager
     */
    private curl_manager $curlmanager;

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
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function run(stdClass $course, check $check): void {
        $this->check = $check;
        $this->curlmanager = new curl_manager($this->check);

        // Check the course summary for links.
        $this->check_course_summary($course);

        // Get all unique modules in the course.
        $modinfo = get_fast_modinfo($course);
        $modules = $this->get_unique_modnames($course, $modinfo);

        foreach ($modules as $modname) {
            $this->check_module_instances($modinfo, $course, $modname);
        }
    }

    /**
     *
     * Check urls in Modules.
     *
     * @param course_modinfo $modinfo
     * @param stdClass $course
     * @param string $modname
     * @return void
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    protected function check_module_instances(course_modinfo $modinfo, stdClass $course, string $modname): void {
        $instances = get_all_instances_in_courses($modname, [$course->id => $course]);
        foreach ($instances as $mod) {
            $cm = $modinfo->get_cm($mod->coursemodule);
            $target = resolution_link_helper::get_target($cm, 'coursechecker_links');
            $resolutionlink = resolution_link_helper::get_link_to_modedit_or_view_page($cm->modname, $cm->id);

            switch ($modname) {
                case self::MOD_TYPE_URL:
                    $this->check_urls_with_resolution_url([$mod->externalurl], $resolutionlink, $target);
                    break;

                case self::MOD_TYPE_BOOK:
                    $this->check_book_chapters($mod);
                    break;

                case self::MOD_TYPE_WIKI:
                    $subwikis = $this->get_subwikis($mod->id);
                    foreach ($subwikis as $subwiki) {
                        $this->check_wiki_pages($subwiki->id);
                    }
                    break;

                default:
                    $this->check_module_properties($mod, $resolutionlink, $target);
                    break;
            }
        }
    }

    /**
     *
     * Check urls in properties.
     *
     * @param stdClass $mod
     * @param string $resolutionlink
     * @param string|null $target
     * @return void
     */
    protected function check_module_properties(stdClass $mod, string $resolutionlink, ?string $target): void {
        if (property_exists($mod, "name")) {
            $this->check_urls_with_resolution_url($this->get_urls_from_text($mod->name), $resolutionlink, $target);
        }
        if (property_exists($mod, "intro")) {
            $this->check_urls_with_resolution_url($this->get_urls_from_text($mod->intro), $resolutionlink, $target);
        }
        if (property_exists($mod, "content")) {
            $this->check_urls_with_resolution_url($this->get_urls_from_text($mod->content), $resolutionlink, $target);
        }
    }

    /**
     *
     * Checks urls in course summary.
     *
     * @param stdClass $course
     * @throws moodle_exception
     */
    protected function check_course_summary(stdClass $course): void {
        $courseurl = resolution_link_helper::get_link_to_course_edit_page($course);
        $this->check_urls_with_resolution_url(
            $this->get_urls_from_text(
                $course->summary
            ),
            $courseurl,
            translation_manager::generate("course_summary", "coursechecker_links")
        );
    }

    /**
     * Check all urls for a single resolution_url
     *
     * @param array $urls
     * @param string|null $link
     * @param string|null $title
     * @return void
     */
    protected function check_urls_with_resolution_url(array $urls, ?string $link = '', ?string $title = ''): void {
        foreach ($urls as $url) {
            $this->curlmanager->check_url($url, $title, $link);
        }
    }

    /**
     * Extract url from a string
     *
     * @param string|null $text
     * @return string[] urls
     */
    protected function get_urls_from_text(?string $text): array {
        // Make sure that $text is not null, but at least an empty string.
        if (!is_string($text) || empty($text)) {
            return [];
        }
        // Be aware that XMLNS can be used.
        // Specially «math xmlns=¨http://www.w3.org/1998/Math/MathML¨».
        if (false !== preg_match_all('#\bhttps?:\/\/[^,\s()<>»¨]+(?:([\w\-]+)|([^,[:punct:],¨»\s]|\/))#', $text, $match)) {
            $match = $match[0];
            // If we have <a href="$url">$url</a> $url is not counted twice.
            return array_unique($match);
        }
        return [];
    }

    /**
     * Gets the unique names of the modules.
     *
     * @param stdClass $course
     * @param course_modinfo|null $modinfo
     * @return array
     * @throws moodle_exception
     */
    protected function get_unique_modnames(stdClass $course, ?course_modinfo $modinfo = null): array {
        if (is_null($modinfo)) {
            $modinfo = get_fast_modinfo($course);
        }
        $modules = [];
        foreach ($modinfo->cms as $cm) {
            $modules[] = $cm->modname;
        }
        // Be sure to check each type of activity ONLY once.
        return array_unique($modules);
    }

    /**
     *
     * Checks urls in book chapters.
     *
     * @param stdClass $mod
     * @throws dml_exception
     * @throws moodle_exception
     */
    protected function check_book_chapters(stdClass $mod): void {
        global $DB;
        $chapters = $DB->get_records('book_chapters', ['bookid' => $mod->id], '', 'id,title,content');
        foreach ($chapters as $chapter) {
            $target = translation_manager::generate('book_chapter', 'coursechecker_links', (object) ["title" => $chapter->title]);
            $url = (new moodle_url('/mod/book/edit.php', ['cmid' => $mod->coursemodule, 'id' => $chapter->id]))->out(false);
            $this->check_urls_with_resolution_url($this->get_urls_from_text($chapter->content), $url, $target);
        }
    }

    /**
     * Returns the subwikis of a wiki.
     *
     * @param int $id The wiki id.
     * @return array
     * @throws dml_exception
     */
    protected function get_subwikis(int $id): array {
        global $DB;

        return $DB->get_records('wiki_subwikis', ['wikiid' => $id]);
    }

    /**
     * Checks wiki pages links in content of a subwiki.
     *
     * @param int $id The subwiki id.
     * @throws dml_exception
     * @throws moodle_exception
     */
    protected function check_wiki_pages(int $id): void {
        global $DB;

        $pages = $DB->get_records('wiki_pages', ['subwikiid' => $id], '', 'id, title, cachedcontent');

        foreach ($pages as $page) {
            $target = translation_manager::generate('wiki_page', 'coursechecker_links', (object) ['title' => $page->title]);
            $resolutionlink = new moodle_url('/mod/wiki/edit.php', ['pageid' => $page->id]);

            $this->check_urls_with_resolution_url($this->get_urls_from_text($page->cachedcontent), $resolutionlink, $target);
        }
    }
}
