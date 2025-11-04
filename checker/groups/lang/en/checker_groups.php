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
 * Strings for component 'checker_groups'.
 *
 * @package    checker_groups
 * @copyright  2025 Simon Gisler, Fernfachhochschule Schweiz (FFHS) <simon.gisler@ffhs.ch>
 * @copyright  based on work by 2019 Liip SA <elearning@liip.ch>
 * @copyright  based on work by 2019 Adrian Perez, Fernfachhochschule Schweiz (FFHS) <adrian.perez@ffhs.ch>
 * @copyright  based on work by 2020 Christoph Karlen, Fernfachhochschule Schweiz (FFHS) <christoph.karlen@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Überprüfung der Gruppenabgabe';
$string['pluginname_help'] = 'This plugin checks assignment activities in a course to ensure correct group submission configuration. It verifies that group mode is properly set, a valid grouping is assigned, and that the grouping contains at least two groups.';

// String specific for the group checker.
$string['groups_deactivated'] = 'Group submission setting is deactivated';
$string['groups_idmissing'] = 'Group submission is active, but no grouping is set';
$string['groups_missing'] = 'Grouping has not been set up correctly';
$string['groups_lessthantwogroups'] = 'Less than 2 groups have been set up for the active grouping';
$string['groups_success'] = 'Group submission setting is well defined';
$string['groups_activity'] = 'Activity "{$a->name}"';
