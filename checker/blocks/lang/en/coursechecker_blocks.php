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

/**
 * Strings for component 'coursechecker_blocks'.
 *
 * @package    coursechecker_blocks
 * @copyright  2025 Simon Gisler, Fernfachhochschule Schweiz (FFHS) <simon.gisler@ffhs.ch>
 * @copyright  based on work by 2019 Liip SA <elearning@liip.ch>
 * @copyright  based on work by 2019 Adrian Perez, Fernfachhochschule Schweiz (FFHS) <adrian.perez@ffhs.ch>
 * @copyright  based on work by 2020 Christoph Karlen, Fernfachhochschule Schweiz (FFHS) <christoph.karlen@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Blockdrawer  check';
$string['pluginname_help'] = 'This check compares the presence of each configured block in the current course with its presence in the reference course and reports blocks that exist in only one of them. Blocks absent from both courses are ignored, and block configuration, position, and region are not compared.';
$string['privacy:metadata'] = 'This check does not store any personal data. Its results are stored by the Course Checker plugin.';

$string['blocks_setting'] = 'Enabled blocks';
$string['blocks_setting_help'] =
        'Define the allowed blocks (must be enabled in <a href="{$a}" target="_blank">Manage blocks</a>) to be checked.';
$string['blocks_comparison'] = '(Reference course: "{$a->valuereference}" | Current course: "{$a->valuecurrent}")';
$string['blocks_success'] = 'The block is correctly inserted in the current course';
$string['blocks_error'] = 'The block is present by mistake or is missing in the current course.';
$string['blocks_activity'] = 'Block "{$a->name}"';
