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
 * Strings for component 'coursechecker_links'.
 *
 * @package    coursechecker_links
 * @copyright  2025 Simon Gisler, Fernfachhochschule Schweiz (FFHS) <simon.gisler@ffhs.ch>
 * @copyright  based on work by 2019 Liip SA <elearning@liip.ch>
 * @copyright  based on work by 2019 Adrian Perez, Fernfachhochschule Schweiz (FFHS) <adrian.perez@ffhs.ch>
 * @copyright  based on work by 2020 Christoph Karlen, Fernfachhochschule Schweiz (FFHS) <christoph.karlen@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Link Überprüfung';
$string['pluginname_help'] = 'Diese Überprüfung extrahiert HTTP- und HTTPS-Links aus der Kursbeschreibung, aus Namen, Einleitungen und Inhalten von Aktivitäten sowie aus Buchkapiteln und Wiki-Seiten und prüft URL-Aktivitäten direkt auf Erreichbarkeit und konfigurierte HTML-Fehlerseitenmuster. Exakte URLs und Domains auf den konfigurierten Ausnahmelisten werden ebenso übersprungen wie durch Moodle-Sicherheitsrichtlinien blockierte Anfragen.';
$string['privacy:metadata'] = 'Diese Überprüfung speichert keine personenbezogenen Daten. Ihre Ergebnisse werden im Haupt-Plugin „Course Checker“ gespeichert.';

// Ergebnisse.
$string['course_summary'] = 'Kurszusammenfassung';
$string['book_chapter'] = 'Buchkapitel: {$a->title}';
$string['wiki_page'] = 'Wiki-Seite: {$a->title}';
$string['filetype_unknown'] = 'Unbekannte Datei';
$string['filetype_html'] = 'HTML-Dokument';
$string['filetype_pdf'] = 'PDF';
$string['filetype_zip'] = 'ZIP';
$string['filetype_image'] = 'Bild';

$string['url_code_valid'] = 'Der Link <b>{$a->url}</b> ist erreichbar <b>(Status {$a->http_code})</b>.'; // You can get any curl info or pare_url field in $a.
$string['error_code'] = 'Der Link <b>{$a->url}</b> konnte nicht aufgerufen werden <b>(Fehler {$a->http_code})</b>.'; // You can get any curl info or pare_url field in $a.
$string['error_curl'] = 'Verbindungsfehler bei <b>{$a->url}: {$a->curl_error} (Code {$a->curl_errno})</b>.'; // You can get any curl info or pare_url field in $a.
$string['error_undefined'] = 'Der Link ist <b>ungültig oder hat ein fehlerhaftes Format</b>.';
$string['error_httpsecurity'] = 'Der Link <b>{$a}</b> ist aufgrund der <b>Moodle-Sicherheitsrichtlinien blockiert</b> und wurde daher <b>nicht geprüft</b>.';
$string['domain_is_whitelisted'] = 'Die Domain <b>{$a->host}</b> steht auf der <b>Ausnahmeliste</b> (Whitelist). Der Link <b>{$a->url}</b> wurde daher <b>nicht geprüft</b>.';
$string['url_is_whitelisted'] = 'Der Link <b>{$a->url}</b> steht auf der <b>Ausnahmeliste</b> (Whitelist) und wurde <b>nicht geprüft</b>.';
$string['html_error_detected'] = 'Der Link <b>{$a->url}</b> scheint auf eine <b>Fehlerseite</b> zu führen, obwohl die Verbindung erfolgreich war (Status {$a->http_code}). <b>Bitte prüfen Sie den Link manuell.</b>';

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
$string['html_error_regex_setting'] = 'Regex zur Erkennung von HTML-Fehlern';
$string['html_error_regex_setting_desc'] = 'Regulärer Ausdruck zur Erkennung von Fehlerseiten, die dennoch HTTP 200 zurückgeben. Beispiel: Seiten, die "Fehler 404" oder "Seite nicht gefunden" enthalten.';
