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
 * Strings for component 'coursechecker_links'.
 *
 * @package    coursechecker_links
 * @copyright  2025 Simon Gisler, Fernfachhochschule Schweiz (FFHS) <simon.gisler@ffhs.ch>
 * @copyright  based on work by 2019 Liip SA <elearning@liip.ch>
 * @copyright  based on work by 2019 Adrian Perez, Fernfachhochschule Schweiz (FFHS) <adrian.perez@ffhs.ch>
 * @copyright  based on work by 2020 Christoph Karlen, Fernfachhochschule Schweiz (FFHS) <christoph.karlen@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Links check';
$string['pluginname_help'] = 'This plugin scans course content—including summaries, modules, books, wikis, and URLs—for hyperlinks and checks their validity.';
$string['privacy:metadata'] = 'The links check does not store any personal data. The check results are stored in the course checker plugin.';

// Results.
$string['course_summary'] = 'Course summary';
$string['book_chapter'] = 'Book Chapter: {$a->title}';
$string['wiki_page'] = 'Wiki Page: {$a->title}';

$string['url_code_valid'] =
        '{$a->url} is valid (Code {$a->http_code})'; // You can get any curl info or pare_url field in $a.
$string['error_code'] =
        'HTTP Error {$a->http_code} on {$a->url}'; // You can get any curl info or pare_url field in $a.
$string['error_curl'] =
        'cURL Error {$a->curl_errno} {$a->curl_error} on {$a->url}'; // You can get any curl info or pare_url field in $a.
$string['error_undefined'] = 'A undefined error with the link occurred';
$string['error_httpsecurity'] =
        'The given domain {$a} is blacklisted by checking its address and port number against the black/white lists in Moodle HTTP security.';
$string['domain_is_whitelisted'] = 'The domain {$a->host} is whitelisted for {$a->url}';
$string['url_is_whitelisted'] = 'The domain {$a->host} is whitelisted for {$a->url}';

// Settings.
$string['timeout_setting'] = 'cURL timeout';
$string['timeout_setting_desc'] = 'Time to connect to the server and exchange data.';
$string['connect_timeout_setting'] = 'cURL connection timeout';
$string['connect_timeout_setting_desc'] = 'Time to connect to the server.';
$string['useragent_setting'] = 'User Agent';
$string['useragent_setting_desc'] = 'User Agents tell a website what browser is being used.';
$string['url_whitelist_setting'] = 'URL whitelist';
$string['url_whitelist_setting_desc'] =
        'This list whitelists the url only. Please add one URL per line e.g. <code>https://moodle.org</code>';
$string['domain_whitelist_setting'] = 'Domain whitelist';
$string['domain_whitelist_setting_desc'] =
        'This list whitelists the whole domain. Please add one URL per line e.g. <code>https://moodle.org</code>';
