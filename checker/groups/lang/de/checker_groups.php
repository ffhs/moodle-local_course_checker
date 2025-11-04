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

$string['pluginname'] = 'Gruppenabgabe Überprüfung';
$string['pluginname_help'] = 'Dieses Plugin überprüft die Aufgabenaktivitäten in einem Kurs, um die korrekte Konfiguration der Gruppenübermittlung sicherzustellen. Es überprüft, ob der Gruppenmodus korrekt eingestellt ist, eine gültige Gruppierung zugewiesen ist und ob die Gruppierung mindestens zwei Gruppen enthält.';

// String specific for the group checker.
$string['groups_deactivated'] = 'Die Einstellung für die Gruppenabgabe ist deaktiviert.';
$string['groups_idmissing'] = 'Die Gruppenabgabe ist aktiv, aber es wurde keine Gruppierung festgelegt.';
$string['groups_missing'] = 'Die Gruppierung wurde nicht korrekt eingerichtet.';
$string['groups_lessthantwogroups'] = 'Für die aktive Gruppierung wurden weniger als 2 Gruppen eingerichtet.';
$string['groups_success'] = 'Die Einstellung für die Gruppenabgabe ist korrekt definiert.';
$string['groups_activity'] = 'Aktivität "{$a->name}"';
