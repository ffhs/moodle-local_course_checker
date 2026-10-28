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

namespace coursechecker_links;

use curl;
use local_course_checker\config;
use local_course_checker\db\model\check;
use local_course_checker\translation_manager;

/**
 * Fetch an url and return true if the code is between 200 and 400.
 *
 * @package    coursechecker_links
 * @copyright  2025 Simon Gisler, Fernfachhochschule Schweiz (FFHS) <simon.gisler@ffhs.ch>
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @copyright  based on work by 2019 Liip SA <elearning@liip.ch>
 * @copyright  based on work by 2020 Christoph Karlen, Fernfachhochschule Schweiz (FFHS) <christoph.karlen@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class curl_manager {
    /** @var string Configuration path for the URL request timeout. */
    const TIMEOUT_SETTING = 'coursechecker_links/timeout';
    /** @var int Default value for total CURL request timeout in seconds. */
    const TIMEOUT_DEFAULT = 13;
    /** @var string Configuration path for the CURL connection timeout. */
    const CONNECT_TIMEOUT_SETTING = 'coursechecker_links/connect_timeout';
    /** @var int Default value for CURL connection timeout in seconds. */
    const CONNECT_TIMEOUT_DEFAULT = 5;
    /** @var string Configuration path for the URL whitelist setting. */
    const URL_WHITELIST_SETTING = 'coursechecker_links/url_whitelist';
    /** @var string Default value for URL whitelist (newline-separated). */
    const URL_WHITELIST_DEFAULT = '';
    /** @var string Configuration path for the domain whitelist setting. */
    const DOMAIN_WHITELIST_SETTING = 'coursechecker_links/domain_whitelist';
    /** @var string Default value for domain whitelist */
    const DOMAIN_WHITELIST_DEFAULT = 'www.w3.org';
    /** @var string Configuration path for the CURL user agent string. */
    const USERAGENT_SETTING = 'coursechecker_links/useragent';
    /** @var string Default user agent string for CURL requests. */
    const USERAGENT_DEFAULT = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36';
    /** @var string Configuration path for the html page regex. */
    public const HTML_ERROR_REGEX_SETTING = 'coursechecker_links/html_error_regex';
    /** @var string Default regex pattern for HTML error pages. */
    public const HTML_ERROR_REGEX_DEFAULT = '/(page not found)/i';
    /** @var array List of domain names that should be ignored during link checking. */
    protected array $ignoreddomains;
    /** @var array List of full URLs that should be ignored during link checking. */
    protected array $ignoredurls;
    /** @var check Instance of the check model to report results to. */
    protected check $check;
    /** @var string User agent string used for CURL requests. */
    protected string $htmlerrorregex;
    /** @var array Array of CURL options. */
    private array $curloptions;

    /**
     * Constructor. Loads checker configuration and initializes CURL-related settings.
     *
     * @param check $check The check instance to log results into.
     */
    public function __construct(check $check) {
        $whitelist = (string) config::get(self::URL_WHITELIST_SETTING, self::URL_WHITELIST_DEFAULT);
        $this->ignoredurls = array_filter(array_map('trim', explode("\n", $whitelist)));
        $whitelist = (string) config::get(self::DOMAIN_WHITELIST_SETTING, self::DOMAIN_WHITELIST_DEFAULT);
        $this->ignoreddomains = array_filter(array_map('trim', explode("\n", $whitelist)));
        $this->htmlerrorregex = (string) config::get(self::HTML_ERROR_REGEX_SETTING, self::HTML_ERROR_REGEX_DEFAULT);
        $this->curloptions = [
            "CURLOPT_CONNECTTIMEOUT" => (int) config::get(self::CONNECT_TIMEOUT_SETTING, self::CONNECT_TIMEOUT_DEFAULT),
            "CURLOPT_TIMEOUT" => (int) config::get(self::TIMEOUT_SETTING, self::TIMEOUT_DEFAULT),
            "CURLOPT_FOLLOWLOCATION" => 1, // Follow redirects.
            "CURLOPT_MAXREDIRS" => 3, // Maximal number of redirects 301, 302.
            "CURLOPT_USERAGENT" => (string) config::get(self::USERAGENT_SETTING, self::USERAGENT_DEFAULT),
            "CURLOPT_SSL_VERIFYHOST" => 2,
            "CURLOPT_SSL_VERIFYPEER" => 1,
            "CURLOPT_ENCODING" => "gzip",
        ];
        $this->check = $check;
    }

    /**
     * Checks a given URL using CURL and fallback mechanisms.
     * Logs results to the provided check object.
     *
     * @param string $url The URL to check.
     * @param string $title The title of the resource being checked.
     * @param string $link The link to the Moodle resource.
     */
    public function check_url(string $url, string $title, string $link): void {
        $parseurl = parse_url($url);
        if (!array_key_exists("host", $parseurl) || $parseurl["host"] == null) {
            $this->check->add_failed($title, $link, translation_manager::generate("error_undefined", "coursechecker_links"));
            return;
        }
        // Check URL against Moodle HTTP security settings.
        $curlhelper = new \core\files\curl_security_helper();
        if ($curlhelper->url_is_blocked($url)) {
            $this->check->add_info(
                $title,
                $link,
                translation_manager::generate("error_httpsecurity", "coursechecker_links", $url),
                ['external_link' => $url]
            );
            return;
        }
        // Skip whitelisted domain.
        if ($this->is_ignored_domain($parseurl["host"])) {
            $this->check->add_info(
                $title,
                $link,
                translation_manager::generate("domain_is_whitelisted", "coursechecker_links", $parseurl + ["url" => $url]),
                ['external_link' => $url]
            );
            return;
        }
        // Skip whitelisted URL.
        if ($this->is_ignored_url($url)) {
            $this->check->add_info(
                $title,
                $link,
                translation_manager::generate("url_is_whitelisted", "coursechecker_links", $parseurl + ["url" => $url]),
                ['external_link' => $url]
            );
            return;
        }
        // Use curl to checks the urls.
        // You can use "$settings['debug'] = true;" to debug the curl request.
        $curl = new curl();

        $httpheader = [];
        $httpheader[] = "Accept-Encoding: gzip, deflate, br";
        $httpheader[] = "Accept:*/*";

        $curloptions = array_merge($this->curloptions, [
            "CURLOPT_HTTPHEADER" => $httpheader,
            "CURLOPT_REFERER" => $url, // Essentially this tells the server which page sent you there.
        ]);

        $curl->head($url, $curloptions);
        $curlinfo = $curl->get_info();

        $code = (int) $curlinfo["http_code"];
        if ($code === 0) {
            // Code 0: timeout or other curl error.
            $context = $parseurl + ["url" => $url, "curl_errno" => $curl->get_errno(), "curl_error" => $curl->error];
            $message = translation_manager::generate("error_curl", "coursechecker_links", $context);
            $this->check->add_failed($title, $link, $message, ['external_link' => $url]);
            return;
        }

        $context = $parseurl + ["url" => $url, "http_code" => $code];
        $customdata['external_link'] = $url;
        if ($code >= 200 && $code < 400) {
            $contenttype = $curlinfo['content_type'] ?? '';
            $customdata['filetype'] = $this->normalize_content_type($contenttype);
            if (is_string($contenttype) && str_contains(strtolower($contenttype), 'text/html')) {
                $htmlcontent = $this->get_html_content($url);

                if ($htmlcontent !== null && !empty($this->htmlerrorregex) && preg_match($this->htmlerrorregex, $htmlcontent)) {
                    $message = translation_manager::generate('html_error_detected', 'coursechecker_links', $context);
                    $this->check->add_warning($title, $link, $message, $customdata);
                    return;
                }
            }

            $message = translation_manager::generate('url_code_valid', 'coursechecker_links', $context);
            $this->check->add_successful($title, $link, $message, $customdata);
            return;
        }

        // Code != 0 means it's a http error.
        $message = translation_manager::generate("error_code", "coursechecker_links", $context);
        $this->check->add_failed($title, $link, $message, $customdata);
    }

    /**
     * Tells if a domain should be skipped.
     *
     * @param string $domain
     * @return boolean
     */
    protected function is_ignored_domain(string $domain): bool {
        foreach ($this->ignoreddomains as $entry) {
            $parsed = parse_url($entry, PHP_URL_HOST) ?? $entry;

            // Normalize to lowercase.
            $parsed = strtolower($parsed);
            $domain = strtolower($domain);

            if ($domain === $parsed || str_ends_with($domain, '.' . $parsed)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Tells if a URL should be skipped.
     *
     * @param string $url
     * @return bool
     */
    protected function is_ignored_url(string $url): bool {
        foreach ($this->ignoredurls as $pattern) {
            // Fnmatch erlaubt *, ? und andere Wildcards.
            if (fnmatch($pattern, $url)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Classifies the response content type.
     *
     * @param string $contenttype
     * @return string
     */
    protected function normalize_content_type(string $contenttype): string {
        $contenttype = strtolower(trim($contenttype));

        if ($contenttype === '') {
            return 'unknown';
        }
        if (str_contains($contenttype, 'text/html') || str_contains($contenttype, 'application/xhtml+xml')) {
            return 'html';
        }
        if (str_contains($contenttype, 'application/pdf')) {
            return 'pdf';
        }
        if (str_contains($contenttype, 'application/zip')) {
            return 'zip';
        }
        if (str_starts_with($contenttype, 'image/')) {
            return 'image';
        }

        return 'other';
    }

    /**
     * Fetches the HTML content of a URL using CURL GET.
     *
     * @param string $url The URL to fetch.
     * @return string|null The response body, or null on failure.
     */
    protected function get_html_content(string $url): ?string {
        $curl = new curl();

        $httpheader = [];
        $httpheader[] = 'Accept-Encoding: gzip, deflate, br';
        $httpheader[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';

        $curloptions = array_merge($this->curloptions, [
            'CURLOPT_HTTPHEADER' => $httpheader,
            'CURLOPT_REFERER' => $url,
        ]);

        $content = $curl->get($url, null, $curloptions);

        $info = $curl->get_info();
        $code = (int) ($info['http_code'] ?? 0);

        if ($code >= 200 && $code < 400 && is_string($content)) {
            return $content;
        }

        return null;
    }
}
