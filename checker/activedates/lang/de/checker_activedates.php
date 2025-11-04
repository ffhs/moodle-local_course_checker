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
 * Strings for component 'checker_activedates'.
 *
 * @package    checker_activedates
 * @copyright  2025 Simon Gisler, Fernfachhochschule Schweiz (FFHS) <simon.gisler@ffhs.ch>
 * @copyright  based on work by 2019 Liip SA <elearning@liip.ch>
 * @copyright  based on work by 2019 Adrian Perez, Fernfachhochschule Schweiz (FFHS) <adrian.perez@ffhs.ch>
 * @copyright  based on work by 2020 Christoph Karlen, Fernfachhochschule Schweiz (FFHS) <christoph.karlen@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Aktive Termine Überprüfung';
$string['pluginname_help'] = 'Dieses Plugin prüft, ob für einen Kurs sowohl ein Startdatum als auch ein Enddatum definiert ist. Es ist als Teil eines Workflows zur Kursqualitätssicherung konzipiert und stellt sicher, dass alle Kurse klar definierte zeitliche Grenzen haben.';
// String specific for the activedates checker.
$string['activedates_setting_modules'] = 'Aktivierte Module';
$string['activedates_setting_modules_help'] =
    'Definieren Sie die erlaubten Module  (müssen in <a href="/admin/modules.php" target="_blank">Aktivitäten verwalten</a> aktiviert sein), die auf aktive Termine geprüft werden sollen.';
$string['activedates_noactivedates'] = 'Es sollten keine aktivierten Termine im Abschnitt "Aktivitätsabschluss" vorhanden sein.';
$string['activedates_noactivedatesinactivity'] =
    'Es sollten keine aktivierten Termine in der Aktivität vom Typ {$a->modtype} vorhanden sein. Überprüfen Sie die folgenden Felder: {$a->adateissetin}';
$string['activedates_success'] = 'Die Aktivität {$a} ist korrekt konfiguriert.';
