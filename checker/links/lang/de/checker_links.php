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
 * Strings for component 'checker_links'.
 *
 * @package    checker_links
 * @copyright  2025 Simon Gisler, Fernfachhochschule Schweiz (FFHS) <simon.gisler@ffhs.ch>
 * @copyright  based on work by 2019 Liip SA <elearning@liip.ch>
 * @copyright  based on work by 2019 Adrian Perez, Fernfachhochschule Schweiz (FFHS) <adrian.perez@ffhs.ch>
 * @copyright  based on work by 2020 Christoph Karlen, Fernfachhochschule Schweiz (FFHS) <christoph.karlen@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Link Überprüfung';
$string['pluginname_help'] = 'Dieses Plugin durchsucht Kursinhalte - einschließlich Zusammenfassungen, Module, Bücher, Wikis und URLs - nach Hyperlinks und überprüft deren Gültigkeit.';

// Ergebnisse.
$string['course_summary'] = 'Kurszusammenfassung';
$string['book_chapter'] = 'Buchkapitel: {$a->title}';
$string['wiki_page'] = 'Wiki-Seite: {$a->title}';

$string['url_code_valid'] = '{$a->url} ist gültig (Code {$a->http_code})'; // You can get any curl info or pare_url field in $a.
$string['error_code'] = 'HTTP-Fehler {$a->http_code} auf {$a->url}'; // You can get any curl info or pare_url field in $a.
$string['error_curl'] = 'cURL-Fehler {$a->curl_errno} {$a->curl_error} auf {$a->url}'; // You can get any curl info or pare_url field in $a.
$string['error_undefined'] = 'Ein undefinierter Fehler mit dem Link ist aufgetreten.';
$string['error_httpsecurity'] = 'Die angegebene Domain {$a} ist gesperrt, da ihre Adresse und Portnummer mit den Black-/Whitelist-Regeln der Moodle-HTTP-Sicherheitsrichtlinien abgeglichen wurden.';
$string['domain_is_whitelisted'] = 'Die Domain {$a->host} ist für {$a->url} auf der Whitelist.';
$string['url_is_whitelisted'] = 'Die Domain {$a->host} ist für {$a->url} auf der Whitelist.';

// Einstellungen.
$string['timeout_setting'] = 'cURL-Timeout';
$string['timeout_setting_desc'] = 'Zeitspanne für die Verbindung zum Server und den Datenaustausch.';
$string['connect_timeout_setting'] = 'cURL-Verbindungs-Timeout';
$string['connect_timeout_setting_desc'] = 'Zeitspanne für die Herstellung der Verbindung zum Server.';
$string['useragent_setting'] = 'User-Agent';
$string['useragent_setting_desc'] = 'Der User-Agent teilt einer Webseite mit, welcher Browser verwendet wird.';
$string['url_whitelist_setting'] = 'URL-Whitelist';
$string['url_whitelist_setting_desc'] = 'Diese Liste setzt nur die angegebene URL auf die Whitelist. Bitte geben Sie eine URL pro Zeile ein, z. B. <code>https://moodle.org</code>';
$string['domain_whitelist_setting'] = 'Domain-Whitelist';
$string['domain_whitelist_setting_desc'] = 'Diese Liste setzt die gesamte Domain auf die Whitelist. Bitte geben Sie eine URL pro Zeile ein, z. B. <code>https://moodle.org</code>';
