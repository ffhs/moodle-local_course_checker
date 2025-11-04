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
 * Strings for component 'checker_subheadings'.
 *
 * @package    checker_subheadings
 * @copyright  2025 Simon Gisler, Fernfachhochschule Schweiz (FFHS) <simon.gisler@ffhs.ch>
 * @copyright  based on work by 2019 Liip SA <elearning@liip.ch>
 * @copyright  based on work by 2019 Adrian Perez, Fernfachhochschule Schweiz (FFHS) <adrian.perez@ffhs.ch>
 * @copyright  based on work by 2020 Christoph Karlen, Fernfachhochschule Schweiz (FFHS) <christoph.karlen@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Label-Untertitel Überprüfung';
$string['pluginname_help'] = 'Der Subheading Checker prüft, ob alle Label-Ressourcen in einem Kurs einheitliche Formatierungsstandards einhalten.';

// String specific for the subheadings checker.
$string['subheadings_wrongfirsthtmltag'] = 'Das erste HTML-Tag ist kein {$a->htmltag}.';
$string['subheadings_iconmissing'] = 'Das Icon fehlt im ersten HTML-Tag.';
$string['subheadings_generalerror'] = 'Es gab ein Problem bei der Ausführung dieser Prüfung.';
$string['subheadings_success'] = 'Dieses Label hat eine passende Untertitel und ein Icon.';
$string['subheadings_labelignored'] = 'Dieses Label wird aufgrund der Whitelist in der Plugin-Konfiguration ignoriert.';

$string['checker_subheadings_setting_whitelist'] = 'Whitelist für Untertitel';
$string['checker_subheadings_setting_whitelist_help'] = 'Bitte fügen Sie eine Zeichenfolge pro Zeile hinzu. Beispiel: "Liebe(r) Modulentwickler".';
