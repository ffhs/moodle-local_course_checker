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
 * General overview.
 *
 * @package    local_course_checker
 * @copyright  2025 Simon Gisler, Fernfachhochschule Schweiz (FFHS) <simon.gisler@ffhs.ch>
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

require_login();


// Get the course ID from the URL.
$courseid = required_param('courseid', PARAM_INT);

// Try to get the course from the database.
try {
    $course = get_course($courseid);
    require_login($course);
} catch (Exception $e) {
    // Use moodle core error messages.
    throw new moodle_exception('courseidnotfound', 'error', '', 'Invalid course'); // Course doesn't exist in DB.
}

$context = context_course::instance($courseid);

// Capability-Check with error handling.
try {
    require_capability('local/course_checker:view', $context);
} catch (required_capability_exception $e) {
    // User does not have the required authorization.
    throw new moodle_exception('nopermissions', 'error', '', 'view course checker');
}

$PAGE->set_context($context);
$PAGE->set_course($course);


// Set up the page.
$PAGE->set_url(new moodle_url('/local/course_checker/index.php', ['courseid' => $courseid]));
$PAGE->set_title(get_string('pluginname', 'local_course_checker'));
$PAGE->set_heading(get_string('pluginname', 'local_course_checker'));
$PAGE->requires->css('/local/course_checker/css/styles.css');
// Data for the Mustache template.

$output = $PAGE->get_renderer('local_course_checker');

// Output the page header.
echo $OUTPUT->header();

$checkerexists = $output->init($course);
echo $output->render_header();
if ($checkerexists) {
    echo $output->render_activity();
    echo $output->render_result();
}

echo $OUTPUT->footer();
