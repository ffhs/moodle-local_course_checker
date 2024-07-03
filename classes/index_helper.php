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
 * Empty index page
 *
 * @package    local_course_checker
 * @copyright  2024 stefan.dani <stefan.dani@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_course_checker;

use coding_exception;
use context_system;
use dml_exception;
use lang_string;
use moodle_exception;
use moodle_url;

/**
 * Moodle page helper class.
 *
 * @package    local_course_checker
 * @copyright  2024 stefan.dani <stefan.dani@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class index_helper {

    /**
     * @var index_helper|null
     */
    private static ?index_helper $helper = null;

    /**
     * Get instance.
     *
     * @return index_helper
     */
    public static function get_instance(): index_helper {
        if (!self::$helper) {
            self::$helper = new self();
        }
        return self::$helper;
    }

    /**
     * Convert a path to an url.
     *
     * @param string $path
     * @return string
     */
    public function convert_path_to_url(string $path): string {
        return '/' . substr($path, strpos($path, 'local'));
    }

    /**
     * Initiate and set up the page.
     *
     * @param string $filepath
     * @return void
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function generate_moodle_page(string $filepath): void {
        global $PAGE, $OUTPUT;

        $basedirectory = dirname(__FILE__, 3);
        $filename = 'index.php';
        $parents = $this->scan_parent_directories($filepath, $basedirectory, $filename, true);

        // Set the context.
        $context = context_system::instance();
        $PAGE->set_context($context);

        // Convert the file path to URL.
        $url = $this->convert_path_to_url($filepath);
        $PAGE->set_url($url);

        // Define component name.
        $name = basename(dirname($filepath));

        // Set the title.
        $PAGE->set_title($this->get_component_name($name));

        // Generate the navigation bar.
        $this->generate_moodle_navbar($parents, $name);

        echo $OUTPUT->header();
        echo $OUTPUT->heading($this->get_component_name($name));
    }

    /**
     * Generate the navigation bar.
     *
     * @param array $parents
     * @param string $currentcomponent
     * @return void
     * @throws moodle_exception
     */
    public function generate_moodle_navbar(array $parents, string $currentcomponent): void {
        global $PAGE;
        // Generate the navigation bar.
        $parents = array_reverse($parents);
        foreach ($parents as $parent) {
            $name = basename(dirname($parent));
            $PAGE->navbar->add($this->get_component_name($name), new moodle_url($parent));
        }
        $PAGE->navbar->add($this->get_component_name($currentcomponent));
    }

    /**
     * Output the moodle footer.
     *
     * @return void
     */
    public function generate_moodle_footer(): void {
        global $OUTPUT;
        echo $OUTPUT->footer();
    }

    /**
     * Scan parent Directories.
     *
     * @param string $currentdir
     * @param string $stopdir
     * @param string $filesearch
     * @param bool $returnasurl
     * @return array
     */
    public function scan_parent_directories(
        string $currentdir,
        string $stopdir,
        string $filesearch,
        bool $returnasurl = false
    ): array {
        $parentdirs = [];
        $parentdir = dirname($currentdir, 2);
        while ($parentdir !== $stopdir) {
            $filepath = $parentdir . '/' . $filesearch;
            if (!file_exists($filepath)) {
                break;
            }
            if ($returnasurl) {
                $parentdirs[] = $this->convert_path_to_url($filepath);
            } else {
                $parentdirs[] = $filepath;
            }
            $parentdir = dirname($parentdir);
        }
        return $parentdirs;
    }

    /**
     * Get component name.
     *
     * @param String $component
     * @return lang_string|string
     * @throws coding_exception
     */
    public function get_component_name(String $component): lang_string|string {
        if ($component == "course_checker") {
            return get_string('course_checker', 'local_course_checker');
        }
        return get_string('course_checker_'.$component, 'local_course_checker');
    }
}
