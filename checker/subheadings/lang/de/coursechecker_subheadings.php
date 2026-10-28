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

$string['pluginname'] = 'Untertitel in Text- und Medienfeldern Überprüfung';
$string['pluginname_help'] = 'Diese Überprüfung untersucht sichtbare Text- und Medienfelder und verlangt als erstes Inhaltselement eine <code>h4</code>-Überschrift mit einem Font-Awesome-Icon-Token. Andere Aktivitätstypen und verborgene Aktivitäten werden ignoriert; Inhalte mit einer konfigurierten Whitelist-Zeichenfolge werden ohne diese Kriterien akzeptiert.';
$string['privacy:metadata'] = 'Diese Überprüfung speichert keine personenbezogenen Daten. Ihre Ergebnisse werden im Haupt-Plugin „Course Checker“ gespeichert.';

// String specific for the subheadings checker.
$string['subheadings_wrongfirsthtmltag'] = 'Das erste HTML-Tag ist kein {$a->htmltag}.';
$string['subheadings_iconmissing'] = 'Das Icon fehlt im ersten HTML-Tag.';
$string['subheadings_generalerror'] = 'Es gab ein Problem bei der Ausführung dieser Prüfung.';
$string['subheadings_success'] = 'Dieses Text- und Medienfeld hat einen gültigen Untertitel und ein Icon.';
$string['subheadings_labelignored'] = 'Dieses Label wird aufgrund der Whitelist in der Plugin-Konfiguration ignoriert.';

$string['subheadings_setting_whitelist'] = 'Whitelist für Untertitel';
$string['subheadings_setting_whitelist_help'] = 'Bitte fügen Sie eine Zeichenfolge pro Zeile hinzu. Beispiel: "Liebe(r) Modulentwickler".';
