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
 * Strings for component 'coursechecker_subheadings'.
 *
 * @package    coursechecker_subheadings
 * @copyright  2025 Simon Gisler, Fernfachhochschule Schweiz (FFHS) <simon.gisler@ffhs.ch>
 * @copyright  based on work by 2019 Liip SA <elearning@liip.ch>
 * @copyright  based on work by 2019 Adrian Perez, Fernfachhochschule Schweiz (FFHS) <adrian.perez@ffhs.ch>
 * @copyright  based on work by 2020 Christoph Karlen, Fernfachhochschule Schweiz (FFHS) <christoph.karlen@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Label subheadings check';
$string['pluginname_help'] = 'The Subheading Checker verifies that all label resources in a course follow consistent formatting standards.';
$string['privacy:metadata'] = 'The label subheadings check does not store any personal data. The check results are stored in the course checker plugin.';

// String specific for the subheadings checker.
$string['subheadings_wrongfirsthtmltag'] = 'The first html-tag is not a {$a->htmltag}';
$string['subheadings_iconmissing'] = 'The icon is missing in the first html-tag';
$string['subheadings_generalerror'] = 'There was a problem executing this check';
$string['subheadings_success'] = 'This label has a nice subheading and icon';
$string['subheadings_labelignored'] = 'This label is ignored due to whitelist in plugin configuration.';

$string['subheadings_setting_whitelist'] = 'Subheading checker strings whitelist';
$string['subheadings_setting_whitelist_help'] = 'Please add one string per line. Example: "Liebe(r) Modulentwickler".';
