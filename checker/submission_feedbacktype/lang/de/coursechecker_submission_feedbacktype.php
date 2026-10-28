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
 * Strings for component 'coursechecker_submission_feedbacktype'.
 *
 * @package    coursechecker_submission_feedbacktype
 * @copyright  2026 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Feedbacktypen bei Aufgaben Überprüfung';
$string['pluginname_help'] = 'Diese Überprüfung untersucht Aufgaben mit aktiviertem Abgabetyp „Texteingabe online“, „Dateiabgabe“ oder „Kaltura-Videoabgabe“ und verlangt mindestens einen aktivierten Feedbacktyp. Aufgaben ohne einen dieser drei Abgabetypen werden ignoriert; andere aktivierte Abgabetypen allein lösen die Überprüfung nicht aus.';
$string['privacy:metadata'] = 'Diese Überprüfung speichert keine personenbezogenen Daten. Ihre Ergebnisse werden im Haupt-Plugin „Course Checker“ gespeichert.';
$string['submission_feedbacktype_error'] = 'Die Aufgabe hat Abgabetypen aktiviert, aber es ist kein Feedback-Typ aktiviert. Bitte aktivieren Sie mindestens einen Feedback-Typen.';
$string['submission_feedbacktype_success'] = 'Die Aufgabe hat mindestens einen Feedback-Typen aktiviert.';
