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
 * Strings for component 'coursechecker_quiz'.
 *
 * @package    coursechecker_quiz
 * @copyright  2025 Simon Gisler, Fernfachhochschule Schweiz (FFHS) <simon.gisler@ffhs.ch>
 * @copyright  based on work by 2019 Liip SA <elearning@liip.ch>
 * @copyright  based on work by 2019 Adrian Perez, Fernfachhochschule Schweiz (FFHS) <adrian.perez@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Quiz Überprüfung';
$string['pluginname_help'] = 'Diese Überprüfung vergleicht bei jedem Quiz im Kurs die maximale Bewertung mit der Gesamtpunktzahl der Fragen. Quiz-Aktivitäten, deren Namen dem konfigurierten regulären Ausdruck der Ausnahmeliste entsprechen, werden nicht verglichen.';
$string['privacy:metadata'] = 'Diese Überprüfung speichert keine personenbezogenen Daten. Ihre Ergebnisse werden im Haupt-Plugin „Course Checker“ gespeichert.';

$string['regex_whitelist'] = 'Regex Quiz Titel Whitelist';
$string['regex_whitelist_desc'] = 'Geben Sie einen regulären Ausdruck ein. Quiz-Aktivitäten, deren Namen diesem Muster entsprechen, werden von der Überprüfung ausgenommen.';
$string['quiz_in_whitelist'] = 'Das Quiz wurde nicht überprüft, da es der Whitelist entspricht.';
$string['quiz_grade_sum_error'] = 'Die maximale Bewertung ({$a->grade}) und die Gesamtsumme der Punkte ({$a->sumgrades}) sollten in diesem Quiz identisch sein.';
$string['quiz_grade_sum_success'] = 'Dieses Quiz ist korrekt konfiguriert.';
$string['quiz_activity'] = 'Aktivität: {$a->name} ({$a->modname})';
