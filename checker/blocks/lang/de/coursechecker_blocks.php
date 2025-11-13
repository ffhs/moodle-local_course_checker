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
 * Strings for component 'local_course_checker'.
 *
 * @package    coursechecker_blocks
 * @copyright  2025 Simon Gisler, Fernfachhochschule Schweiz (FFHS) <simon.gisler@ffhs.ch>
 * @copyright  based on work by 2019 Liip SA <elearning@liip.ch>
 * @copyright  based on work by 2019 Adrian Perez, Fernfachhochschule Schweiz (FFHS) <adrian.perez@ffhs.ch>
 * @copyright  based on work by 2020 Christoph Karlen, Fernfachhochschule Schweiz (FFHS) <christoph.karlen@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Block Überprüfung';
$string['pluginname_help'] = 'Dieses Plugin vergleicht die aktivierten Blöcke eines Kurses mit einem Referenzkurs, um ein einheitliches Blocklayout sicherzustellen. Es prüft auf fehlende oder nicht übereinstimmende Blöcke und trägt dazu bei, eine standardisierte Kursstruktur auf der gesamten Plattform aufrechtzuerhalten.';
$string['privacy:metadata'] = 'Das Plugin „Block Überprüfung“ speichert keine personenbezogenen Daten. Die Prüfergebnisse werden im Haupt-Plugin „Course Checker“ gespeichert.';

$string['blocks_setting'] = 'Aktivierte Blöcke';
$string['blocks_setting_help'] =
    'Definieren Sie die erlaubten Blöcke (müssen in <a href="{$a}" target="_blank">Blöcke verwalten</a> aktiviert sein), die geprüft werden sollen.';
$string['blocks_comparison'] = '(Referenzkurs: "{$a->valuereference}" | Aktueller Kurs: "{$a->valuecurrent}")';
$string['blocks_success'] = 'Der Block ist im aktuellen Kurs korrekt eingefügt.';
$string['blocks_error'] = 'Der Block ist entweder fälschlicherweise vorhanden oder fehlt im aktuellen Kurs.';
$string['blocks_activity'] = 'Block "{$a->name}"';
