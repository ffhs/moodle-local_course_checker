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

namespace coursechecker_referencesettings;

use coding_exception;
use context_course;
use dml_exception;
use local_course_checker\model\check_plugin_interface;
use local_course_checker\db\model\check;
use local_course_checker\model\checker_config_trait;
use local_course_checker\resolution_link_helper;
use local_course_checker\translation_manager;
use moodle_exception;
use stdClass;

/**
 * Checking the course settings compared to a reference course
 *
 * @package    coursechecker_referencesettings
 * @copyright  2025 Simon Gisler, Fernfachhochschule Schweiz (FFHS) <simon.gisler@ffhs.ch>
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @copyright  based on work by 2019 Liip SA <elearning@liip.ch>
 * @copyright  based on work by 2019 Christoph Karlen, Fernfachhochschule Schweiz (FFHS) <christoph.karlen@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class checker implements check_plugin_interface {
    use checker_config_trait;

    /** Config path for the reference course ID. */
    const REFERENCE_COURSE = 'local_course_checker/referencecourseid';
    /** Default value for the reference course ID. */
    const REFERENCE_COURSE_DEFAULT = 1;
    /** Config path for the reference course settings (e.g., checklist of options). */
    const REFERENCE_COURSE_SETTINGS = 'coursechecker_referencesettings/checklist';
    /** Default value for the reference course settings. */
    const REFERENCE_COURSE_SETTINGS_DEFAULT = ['format' => 1];
    /** Config path for enabling filtering of reference course settings. */
    const REFERENCE_COURSE_FILTER_ENABLED = 'coursechecker_referencesettings/filter';
    /** Default value for enabling the reference course filter. */
    const REFERENCE_COURSE_FILTER_ENABLED_DEFAULT = false;
    /** Config path for enabling comparison of course format options. */
    const REFERENCE_COURSE_FORMAT_OPTION_ENABLED = 'coursechecker_referencesettings/formatoptions';
    /** Default value for enabling course format option comparison. */
    const REFERENCE_COURSE_FORMAT_OPTION_ENABLED_DEFAULT = false;

    /**
     * @var check The result of the check.
     */
    protected check $check;

    /** @var int $referencecourseid from checker settings */
    protected int $referencecourseid;

    /** @var array $referencecourseid from checker settings */
    protected array $referencesettings = [];

    /** @var array|bool $referencefilterenabled from checker settings */
    protected array|bool $referencefilterenabled;

    /** @var array|bool $referenceformatoptionsenabled from checker settings */
    protected array|bool $referenceformatoptionsenabled;

    /**
     * Initialize checker by setting it up with the configuration
     *
     * @throws dml_exception
     */
    public function init(): void {
        // Load settings.
        $this->referencecourseid = (int) $this->get_config(self::REFERENCE_COURSE, self::REFERENCE_COURSE_DEFAULT);
        $this->referencesettings = explode(',', $this->get_config(
            self::REFERENCE_COURSE_SETTINGS,
            self::REFERENCE_COURSE_SETTINGS_DEFAULT
        ));
        $this->referencefilterenabled = $this->get_config(
            self::REFERENCE_COURSE_FILTER_ENABLED,
            self::REFERENCE_COURSE_FILTER_ENABLED_DEFAULT
        );
        $this->referenceformatoptionsenabled = $this->get_config(
            self::REFERENCE_COURSE_FORMAT_OPTION_ENABLED,
            self::REFERENCE_COURSE_FORMAT_OPTION_ENABLED_DEFAULT
        );
    }

    /**
     * Runs the check
     *
     * @param stdClass $course
     * @param check $check
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function run(stdClass $course, check $check): void {
        // Get active setting checks from configuration.
        $this->init();

        // Initialize check result array.
        $this->check = $check;

        // Get current and reference course configuration.
        $currentcourse = $course;
        $referencecourse = get_course($this->referencecourseid);

        // Check settings like Category, Format, Force Language. See plugin settings for complete list.
        $this->compare_default_course_settings($course, $referencecourse, $currentcourse);

        // Check if the course filters have the same settings as the template reference course.
        $this->compare_course_level_filters($currentcourse, $referencecourse);

        // Compare the course format options.
        $this->compare_course_format_options($currentcourse, $referencecourse);
    }

    /**
     * Generates a translated string comparing a setting between the reference and current course.
     *
     * @param string $setting The name of the setting to compare.
     * @param stdClass $referencecourse The reference course object.
     * @param stdClass $currentcourse The current course object.
     * @return string The translated comparison string.
     */
    private function get_comparison_string(string $setting, stdClass $referencecourse, stdClass $currentcourse): string {
        return translation_manager::generate(
            'referencesettings_comparison',
            'coursechecker_referencesettings',
            ['settingvaluereference' => $referencecourse->$setting, 'settingvaluecurrent' => $currentcourse->$setting]
        );
    }

    /**
     * Generates a translated string comparing a filter setting between two courses.
     *
     * @param object $filterinforeference The filter info from the reference course.
     * @param object $filterinfocurrent The filter info from the current course.
     * @return string The translated filter comparison string.
     */
    private function get_filter_comparison_string(object $filterinforeference, object $filterinfocurrent): string {
        return translation_manager::generate(
            'referencefilter_comparison',
            'coursechecker_referencesettings',
            [
                'filtervaluereference' => $filterinforeference->localstate,
                'filtervaluecurrent' => $filterinfocurrent->localstate,
            ]
        );
    }

    /**
     * Compares default course settings between the current and reference course.
     * Adds results to the check object and updates its status.
     *
     * @param stdClass $course The course object used for linking.
     * @param stdClass $referencecourse The reference course object.
     * @param stdClass $currentcourse The current course object.
     * @throws coding_exception
     * @throws moodle_exception
     */
    protected function compare_default_course_settings(stdClass $course, stdClass $referencecourse, stdClass $currentcourse): void {
        // Run comparison for every attribute.
        foreach ($this->referencesettings as $setting) {
            // Does the attribute exist on both courses?
            if (!property_exists($referencecourse, $setting) || !property_exists($currentcourse, $setting)) {
                $message = translation_manager::generate(
                    'referencesettings_settingismissing',
                    'coursechecker_referencesettings',
                    ['setting' => $setting]
                );
                $this->check->add_failed('', '', $message);
                $this->check->set('status', 'failed');
                continue;
            }

            // Get link to course edit page.
            $link = resolution_link_helper::get_link_to_course_edit_page($course);

            // What are the differences? (if any).
            $comparison = $this->get_comparison_string($setting, $referencecourse, $currentcourse);

            // When the settings are not equal.
            if ($referencecourse->$setting != $currentcourse->$setting) {
                $message = translation_manager::generate(
                    'referencesettings_failing',
                    'coursechecker_referencesettings',
                    [
                        'setting' => $setting,
                        'comparison' => $comparison,
                    ]
                );
                $this->check->add_failed('', $link, $message);
                $this->check->set('status', 'failed');
                continue;
            }

            // When everything is okay.
            $message = translation_manager::generate(
                'referencesettings_success',
                'coursechecker_referencesettings',
                ['setting' => $setting]
            );
            $this->check->add_successful('', $link, $message);
        }
    }

    /**
     * Compares enabled filters between current and reference courses.
     * Reports mismatches and sets check status accordingly.
     *
     * @param stdClass $currentcourse The current course object.
     * @param stdClass $referencecourse The reference course object.
     * @throws coding_exception
     * @throws moodle_exception
     */
    protected function compare_course_level_filters(stdClass $currentcourse, stdClass $referencecourse): void {
        if (!$this->referencefilterenabled) {
            return;
        }

        // Get the course context for the current course and the reference course.
        $currentcontext = context_course::instance($currentcourse->id);
        $referencecontext = context_course::instance($referencecourse->id);

        // Get the list of available filters.
        $currentavailablefilters = filter_get_available_in_context($currentcontext);
        $referenceavailablefilters = filter_get_available_in_context($referencecontext);

        // Count occurring errors.
        $occurringfilterproblems = 0;

        // Get link to course filter page.
        $link = resolution_link_helper::get_link_to_course_filter_page($currentcontext);

        // Count all errors.
        foreach ($referenceavailablefilters as $filterkey => $referencefilterinfo) {
            if (!isset($currentavailablefilters[$filterkey])) {
                $message = translation_manager::generate(
                    'referencefilter_filternotsetincurrentcourse',
                    'coursechecker_referencesettings',
                    ['filterkey' => $filterkey]
                );
                $this->check->add_failed('', $link, $message);
                $this->check->set('status', 'failed');
                continue;
            }
            if ($currentavailablefilters[$filterkey]->localstate != $referencefilterinfo->localstate) {
                // What are the differences? (if any).
                $comparison = $this->get_filter_comparison_string($referencefilterinfo, $currentavailablefilters[$filterkey]);
                $message = translation_manager::generate(
                    'referencefilter_failing',
                    'coursechecker_referencesettings',
                    ['filterkey' => $filterkey,
                    'comparison' => $comparison]
                );
                $this->check->add_failed('', $link, $message);
                $this->check->set('status', 'failed');

                $occurringfilterproblems++;
            }
        }

        // When everything is okay.
        if ($occurringfilterproblems === 0) {
            $message = translation_manager::generate(
                'referencefilter_success',
                'coursechecker_referencesettings'
            );

            $this->check->add_successful('', $link, $message);
        }
    }

    /**
     * Compares format options between the current and reference course.
     * Reports differences and marks the check as failed if mismatches are found.
     *
     * @param stdClass $currentcourse The current course object.
     * @param stdClass $referencecourse The reference course object.
     * @throws coding_exception
     * @throws moodle_exception
     */
    protected function compare_course_format_options(stdClass $currentcourse, stdClass $referencecourse): void {
        global $PAGE, $COURSE;
        if (!$this->referenceformatoptionsenabled) {
            return;
        }
        $occurringoptionproblems = 0;

        $PAGE->set_course($currentcourse);
        $current = course_get_format($COURSE)->get_format_options();
        $PAGE->set_course($referencecourse);
        $reference = course_get_format($COURSE)->get_format_options();
        $link = resolution_link_helper::get_link_to_course_edit_page($currentcourse);

        foreach ($reference as $optionkey => $value) {
            if (!isset($current[$optionkey])) {
                continue;
            }
            if ($value == $current[$optionkey]) {
                continue;
            }
            $comparison = translation_manager::generate(
                'referencesettings_comparison',
                'coursechecker_referencesettings',
                ['settingvaluereference' => $value, 'settingvaluecurrent' => $current[$optionkey]]
            );
            $message = translation_manager::generate(
                'referenceformatoptions_failing',
                'coursechecker_referencesettings',
                ['optionkey' => $optionkey, 'comparison' => $comparison]
            );
            $this->check->add_failed('', $link, $message);
            $this->check->set('status', 'failed');

            $occurringoptionproblems++;
        }

        // When everything is okay.
        if ($occurringoptionproblems === 0) {
            $message = translation_manager::generate(
                'referenceformatoptions_success',
                'coursechecker_referencesettings'
            );
            $this->check->add_successful('', $link, $message);
        }
    }
}
