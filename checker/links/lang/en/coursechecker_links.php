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

$string['pluginname'] = 'Link check';
$string['pluginname_help'] = 'This check extracts HTTP and HTTPS links from the course summary, activity names, introductions and content, book chapters and wiki pages, and directly checks URL activities for reachability and configured HTML error-page patterns. Exact URLs and domains on the configured whitelists are skipped, as are requests blocked by Moodle security policies.';
$string['privacy:metadata'] = 'This check does not store any personal data. Its results are stored by the Course Checker plugin.';

// Results.
$string['course_summary'] = 'Course summary';
$string['book_chapter'] = 'Book Chapter: {$a->title}';
$string['wiki_page'] = 'Wiki Page: {$a->title}';
$string['filetype_unknown'] = 'Unknown file';
$string['filetype_html'] = 'HTML Document';
$string['filetype_pdf'] = 'PDF';
$string['filetype_zip'] = 'ZIP';
$string['filetype_image'] = 'Image';

$string['url_code_valid'] = 'The link <b>{$a->url}</b> is reachable <b>(Status {$a->http_code})</b>.'; // You can get any curl info or pare_url field in $a.
$string['error_code'] = 'The link <b>{$a->url}</b> could not be reached <b>(Error {$a->http_code})</b>.'; // You can get any curl info or pare_url field in $a.
$string['error_curl'] = 'Connection error for <b>{$a->url}: {$a->curl_error} (Code {$a->curl_errno})</b>.'; // You can get any curl info or pare_url field in $a.
$string['error_undefined'] = 'The link is <b>invalid or has an incorrect format</b>.';
$string['error_httpsecurity'] = 'The link <b>{$a}</b> is blocked due to <b>Moodle security policies</b> and was therefore <b>not checked</b>.';
$string['domain_is_whitelisted'] = 'The domain <b>{$a->host}</b> is on the <b>exception list</b> (whitelist). The link <b>{$a->url}</b> was therefore <b>not checked</b>.';
$string['url_is_whitelisted'] = 'The link <b>{$a->url}</b> is on the <b>exception list</b> (whitelist) and was <b>not checked</b>.';
$string['html_error_detected'] = 'The link <b>{$a->url}</b> seems to lead to an <b>error page</b>, although the connection was successful (Status {$a->http_code}). <b>Please check the link manually.</b>';

// Settings.
$string['timeout_setting'] = 'cURL timeout';
$string['timeout_setting_desc'] = 'Time to connect to the server and exchange data.';
$string['connect_timeout_setting'] = 'cURL connection timeout';
$string['connect_timeout_setting_desc'] = 'Time to connect to the server.';
$string['useragent_setting'] = 'User Agent';
$string['useragent_setting_desc'] = 'User Agents tell a website what browser is being used.';
$string['url_whitelist_setting'] = 'URL whitelist';
$string['url_whitelist_setting_desc'] = 'This list whitelists the url only. Please add one URL per line e.g. <code>https://moodle.org</code>';
$string['domain_whitelist_setting'] = 'Domain whitelist';
$string['domain_whitelist_setting_desc'] = 'This list whitelists the whole domain. Please add one URL per line e.g. <code>https://moodle.org</code>';
$string['html_error_regex_setting'] = 'HTML error detection regex';
$string['html_error_regex_setting_desc'] = 'Regular expression used to detect error pages that still return HTTP 200. Example: pages containing "Error 404" or "Page not found"".';
