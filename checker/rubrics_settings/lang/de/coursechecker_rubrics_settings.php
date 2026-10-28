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
 * Strings for component 'coursechecker_rubrics_settings'.
 *
 * @package     coursechecker_rubrics_settings
 * @copyright   2026 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Rubrik Einstellungen Überprüfung';
$string['pluginname_help'] = 'Diese Überprüfung untersucht Aufgaben, für die eine Rubrik als erweiterte Bewertungsmethode aktiviert ist. Als Bewertungstyp müssen Punkte statt einer Skala eingestellt sein. Zudem muss ein aktives Rubrik-Bewertungsformular vorhanden sein. Fehlt dieses oder befindet es sich noch im Entwurfsstatus, wird eine Warnung ausgegeben. Stimmt die maximale Punktzahl der Rubrik nicht mit der maximalen Punktzahl in den Aufgabeneinstellungen überein, wird ein Fehler gemeldet. Aufgaben ohne aktive Rubrik werden nicht überprüft.';
$string['privacy:metadata'] = 'Diese Überprüfung speichert keine personenbezogenen Daten. Ihre Ergebnisse werden im Haupt-Plugin „Course Checker“ gespeichert.';

$string['rubric_grade_type_scale'] = 'Bei Verwendung einer Rubrik muss der Bewertungstyp „Punkte“ sein. Aktuell ist eine Skala eingestellt.';
$string['rubric_grade_type_none'] = 'Bei Verwendung einer Rubrik muss ein Bewertungstyp (Punkte) eingestellt sein.';
$string['rubric_points_mismatch'] = 'Die maximale Punktzahl der Rubrik ({$a->rubric_max}) stimmt nicht mit der maximalen Bewertung in den Aufgabeneinstellungen ({$a->assign_grade}) überein.';
$string['rubric_points_success'] = 'Die Rubrik-Punkte stimmen mit den Einstellungen überein.';
