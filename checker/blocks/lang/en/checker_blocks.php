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
 * Strings for component 'checker_blocks'.
 *
 * @package    checker_blocks
 * @copyright  2025 Simon Gisler, Fernfachhochschule Schweiz (FFHS) <simon.gisler@ffhs.ch>
 * @copyright  based on work by 2019 Liip SA <elearning@liip.ch>
 * @copyright  based on work by 2019 Adrian Perez, Fernfachhochschule Schweiz (FFHS) <adrian.perez@ffhs.ch>
 * @copyright  based on work by 2020 Christoph Karlen, Fernfachhochschule Schweiz (FFHS) <christoph.karlen@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Blocks check';
$string['pluginname_help'] = 'This plugin compares the enabled blocks in a course against a reference course to ensure consistent block layout. It checks for missing or mismatched blocks and helps maintain a standardized course structure across the platform.';

$string['blocks_setting'] = 'Enabled blocks';
$string['blocks_setting_help'] =
        'Define the allowed blocks (must be enabled in <a href="/admin/blocks.php" target="_blank">Manage blocks</a>) to be checked.';
$string['blocks_comparison'] = '(Reference course: "{$a->valuereference}" | Current course: "{$a->valuecurrent}")';
$string['blocks_success'] = 'The block is correctly inserted in the current course';
$string['blocks_error'] = 'The block is present by mistake or is missing in the current course.';
$string['blocks_activity'] = 'Block "{$a->name}"';
