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
$string['pluginname_help'] = 'This check examines visible Text and media area activities and requires their first content element to be an <code>h4</code> heading containing a Font Awesome icon token. Other activity types and hidden activities are ignored, and content containing a configured whitelist string is accepted without these criteria.';
$string['privacy:metadata'] = 'This check does not store any personal data. Its results are stored by the Course Checker plugin.';

// String specific for the subheadings checker.
$string['subheadings_wrongfirsthtmltag'] = 'The first HTML tag is not {$a->htmltag}.';
$string['subheadings_iconmissing'] = 'The icon is missing from the first HTML tag.';
$string['subheadings_generalerror'] = 'There was a problem executing this check.';
$string['subheadings_success'] = 'This Text and media area has a valid subheading and icon.';
$string['subheadings_labelignored'] = 'This label is ignored due to whitelist in plugin configuration.';

$string['subheadings_setting_whitelist'] = 'Subheading checker strings whitelist';
$string['subheadings_setting_whitelist_help'] = 'Please add one string per line. Example: "Liebe(r) Modulentwickler".';
