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
 * Strings for component 'coursechecker_userdata'.
 *
 * @package    coursechecker_userdata
 * @copyright  2025 Simon Gisler, Fernfachhochschule Schweiz (FFHS) <simon.gisler@ffhs.ch>
 * @copyright  based on work by 2019 Liip SA <elearning@liip.ch>
 * @copyright  based on work by 2019 Adrian Perez, Fernfachhochschule Schweiz (FFHS) <adrian.perez@ffhs.ch>
 * @copyright  based on work by 2020 Christoph Karlen, Fernfachhochschule Schweiz (FFHS) <christoph.karlen@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Nutzerdaten Überprüfung';
$string['pluginname_help'] = 'Diese Überprüfung sucht in sichtbaren, aufrufbaren Aktivitäten der konfigurierten unterstützten Typen nach vorhandenen Datenbank-, Glossar-, Wiki- oder Foreninhalten. Sie wird nur für Kurse ausgeführt, die dem konfigurierten Kursmuster entsprechen; nicht unterstützte oder deaktivierte Typen werden ignoriert und Aktivitäten, deren Namen der konfigurierten Regex-Whitelist entsprechen, werden übersprungen.';
$string['privacy:metadata'] = 'Diese Überprüfung speichert keine personenbezogenen Daten. Ihre Ergebnisse werden im Haupt-Plugin „Course Checker“ gespeichert.';

// String spezifisch für die Überprüfung der Nutzerdaten.
$string['userdata_setting_modules'] = 'Aktivierte Module';
$string['userdata_setting_modules_help'] = 'Definieren Sie die erlaubten Module, die auf Nutzerdaten überprüft werden sollen. Die Module müssen unter <a href="{$a}" target="_blank">Aktivitäten verwalten</a> aktiviert sein, die Methode reset_userdata in <code>mod/{modname}/lib.php</code> enthalten und von diesem Plugin unterstützt werden.';
$string['userdata_error'] = 'Es sollten keine Nutzerdaten in der Aktivität {$a} vorhanden sein.';
$string['userdata_success'] = 'Die Aktivität {$a} enthält keine Nutzerdaten.';
$string['userdata_skip'] = 'Die Aktivität "{$a}" wurde nicht überprüft, da sie sich auf der Whitelist befindet.';
$string['userdata_help'] = 'Wenn Sie möchten, dass diese Daten in andere Kurse kopiert werden, müssen Sie diese manuell importieren. Hier sind einige nützliche Anleitungen: <a href="https://docs.moodle.org/38/en/Backup_of_user_data" target="_blank">Backup von Nutzerdaten</a> und <a href="https://docs.moodle.org/38/en/Reusing_activities" target="_blank">Wiederverwendung von Aktivitäten</a>.';
$string['regex_forum_whitelist'] = 'Forum-Whitelist';
$string['regex_forum_whitelist_desc'] = 'Foren, deren Name dem angegebenen Regex-Muster entspricht, werden nicht überprüft.';
