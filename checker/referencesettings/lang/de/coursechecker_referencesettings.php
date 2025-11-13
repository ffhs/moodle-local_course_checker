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
 * Strings for component 'coursechecker_referencesettings'.
 *
 * @package    coursechecker_referencesettings
 * @copyright  2025 Simon Gisler, Fernfachhochschule Schweiz (FFHS) <simon.gisler@ffhs.ch>
 * @copyright  based on work by 2019 Liip SA <elearning@liip.ch>
 * @copyright  based on work by 2019 Adrian Perez, Fernfachhochschule Schweiz (FFHS) <adrian.perez@ffhs.ch>
 * @copyright  based on work by 2020 Christoph Karlen, Fernfachhochschule Schweiz (FFHS) <christoph.karlen@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Referenzeinstellungen Überprüfung';
$string['pluginname_help'] = 'Dieses Plugin vergleicht allgemeine Kurseinstellungen (wie Kategorie, Format, Sprache, Filter und Formatoptionen) des aktuellen Kurses mit denen eines Referenzkurses.';
$string['privacy:metadata'] = 'Das Plugin „Referenzeinstellungen Überprüfung“ speichert keine personenbezogenen Daten. Die Prüfergebnisse werden im Haupt-Plugin „Course Checker“ gespeichert.';

// String specific for the reference course settings checker.
$string['referencesettings_comparison'] =
    ' (Referenzkurs: "{$a->settingvaluereference}" | Aktueller Kurs: "{$a->settingvaluecurrent}")';
$string['referencesettings_settingismissing'] = 'Die Einstellung "{$a->setting}" ist keine Kurseinstellung.';
$string['referencesettings_failing'] = 'Die Einstellung "{$a->setting}" ist nicht korrekt. {$a->comparison}';
$string['referencesettings_success'] = 'Die Einstellung "{$a->setting}" ist korrekt.';
$string['referencesettings_checklist'] = 'Checkliste der Referenzkurs-Einstellungen';
$string['referencesettings_checklist_help'] = 'Bitte wählen Sie eine oder mehrere Einstellungen aus, die mit dem Referenzkurs überprüft werden sollen.';

// String specific for the reference course settings checker filters.
$string['referencefilter_comparison'] =
    ' (Referenzkurs: "{$a->filtervaluereference}" | Aktueller Kurs: "{$a->filtervaluecurrent}")';
$string['referencefilter_failing'] = 'Der Filter "{$a->filterkey}" ist nicht korrekt. {$a->comparison}';
$string['referencefilter_success'] = 'Alle Filter sind im aktuellen Kurs korrekt gesetzt.';
$string['referencefilter_enabled'] = 'Filterprüfung für Referenzeinstellungen aktiviert';
$string['referencefilter_enabled_help'] = 'Bitte aktivieren Sie diese Option, um alle Kursfilter mit dem Referenzkurs zu vergleichen.';
$string['referencefilter_filternotsetincurrentcourse'] = 'Der Filter "{$a->filterkey}" fehlt im aktuellen Kurs.';
$string['referenceformatoptions_failing'] = 'Die Formatoption "{$a->optionkey}" ist nicht korrekt. {$a->comparison}';
$string['referenceformatoptions_success'] = 'Alle Formatoptionen sind im aktuellen Kurs korrekt gesetzt.';
$string['referenceformatoptions_enabled'] = 'Formatoptionen-Prüfung für Referenzeinstellungen aktiviert';
$string['referenceformatoptions_enabled_help'] =
    'Bitte aktivieren Sie diese Option, um alle Formatoptionen des Kurses mit dem Referenzkurs zu vergleichen.';
