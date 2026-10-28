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
 * Strings for component 'coursechecker_activedates'.
 *
 * @package    coursechecker_activedates
 * @copyright  2025 Simon Gisler, Fernfachhochschule Schweiz (FFHS) <simon.gisler@ffhs.ch>
 * @copyright  based on work by 2019 Liip SA <elearning@liip.ch>
 * @copyright  based on work by 2019 Adrian Perez, Fernfachhochschule Schweiz (FFHS) <adrian.perez@ffhs.ch>
 * @copyright  based on work by 2020 Christoph Karlen, Fernfachhochschule Schweiz (FFHS) <christoph.karlen@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Aktive Termine Überprüfung';
$string['pluginname_help'] = 'Diese Überprüfung untersucht sichtbare, aufrufbare Aktivitäten der konfigurierten Typen und meldet aktivierte Abschlussdaten sowie aktivitätsspezifische Verfügbarkeits- und Fristfelder. Kurse, die durch das konfigurierte Kursnamenmuster ausgeschlossen sind, nicht konfigurierte Aktivitätstypen sowie verborgene oder nicht aufrufbare Aktivitäten werden nicht überprüft.';
$string['privacy:metadata'] = 'Diese Überprüfung speichert keine personenbezogenen Daten. Ihre Ergebnisse werden im Haupt-Plugin „Course Checker“ gespeichert.';
// String specific for the activedates checker.
$string['activedates_setting_modules'] = 'Aktivierte Module';
$string['activedates_setting_modules_help'] = 'Definieren Sie die erlaubten Module  (müssen in <a href="{$a}" target="_blank">Aktivitäten verwalten</a> aktiviert sein), die auf aktive Termine geprüft werden sollen.';
$string['activedates_noactivedates'] = 'Das erwartete Abschlussdatum darf nicht aktiviert sein.';
$string['activedates_noactivedatesinactivity'] = 'In der Aktivität vom Typ <b>{$a->modtype}</b> sind Termine in folgenden Feldern aktiviert: <b>{$a->adateissetin}</b>.';
$string['activedates_success'] = 'In der Aktivität {$a} sind keine Termine aktiviert.';
