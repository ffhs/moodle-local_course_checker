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
 * Settings for checking blocks inside the course
 *
 * @package    coursechecker_blocks
 * @copyright  2025 Simon Gisler, Fernfachhochschule Schweiz (FFHS) <simon.gisler@ffhs.ch>
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @copyright  based on work by 2020 Adrian Perez, Fernfachhochschule Schweiz (FFHS) <adrian.perez@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_course_checker\admin\admin_setting_pickblocks;

defined('MOODLE_INTERNAL') || die();

global $CFG;

/** @var admin_settingpage $settings */
$settings;

$visiblename = get_string('blocks_setting', 'coursechecker_blocks');
$url = $CFG->wwwroot . '/admin/blocks.php';
$description = get_string('blocks_setting_help', 'coursechecker_blocks', $url);
$blocks = new admin_setting_pickblocks('coursechecker_blocks/blocks', $visiblename, $description, []);
$settings->add($blocks);
