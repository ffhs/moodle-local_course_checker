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

namespace coursechecker_links;

use curl;
use Exception;
use local_course_checker\db\model\check;
use local_course_checker\model\checker_config_trait;
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
    use checker_config_trait;

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
    /** @var string Default value for domain whitelist*/
    const DOMAIN_WHITELIST_DEFAULT = 'www.w3.org';
    /** @var string Configuration path for the CURL user agent string. */
    const USERAGENT_SETTING = 'coursechecker_links/useragent';
    /** @var string Default user agent string for CURL requests. */
    const USERAGENT_DEFAULT = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36';

    /** @var int Maximum number of seconds to wait while trying to connect. */
    protected int $connecttimeout;
    /** @var int Maximum total number of seconds for the entire CURL request. */
    protected int $timeout;
    /** @var array List of domain names that should be ignored during link checking. */
    protected array $ignoreddomains;
    /** @var array List of full URLs that should be ignored during link checking. */
    protected array $ignoredurls;
    /** @var check Instance of the check model to report results to. */
    protected check $check;
    /** @var string User agent string used for CURL requests. */
    protected string $useragent;

    /**
     * Constructor. Loads checker configuration and initializes CURL-related settings.
     *
     * @param check $check The check instance to log results into.
     */
    public function __construct(check $check) {
        $this->connecttimeout = (int) $this->get_config(self::CONNECT_TIMEOUT_SETTING, self::CONNECT_TIMEOUT_DEFAULT);
        $this->timeout = (int) $this->get_config(self::TIMEOUT_SETTING, self::TIMEOUT_DEFAULT);
        $this->useragent = (string) $this->get_config(self::USERAGENT_SETTING, self::USERAGENT_DEFAULT);
        $urlwhitelist = (string) $this->get_config(self::URL_WHITELIST_SETTING, self::URL_WHITELIST_DEFAULT);
        $this->ignoredurls = array_filter(array_map('trim', explode("\n", $urlwhitelist)));
        $domainwhitelist = (string) $this->get_config(self::DOMAIN_WHITELIST_SETTING, self::DOMAIN_WHITELIST_DEFAULT);
        $this->ignoreddomains = array_filter(array_map('trim', explode("\n", $domainwhitelist)));

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
    public function check_url(string $url, string $title, string $link) {
        $parseurl = parse_url($url);
        if (!array_key_exists("host", $parseurl) || $parseurl["host"] == null) {
            $this->check->set('status', 'failed');
            $this->check->add_failed($title, $link, translation_manager::generate("error_undefined", "coursechecker_links"));
            return;
        }
        // Check URL against Moodle HTTP security settings.
        $curlhelper = new \core\files\curl_security_helper();
        if ($curlhelper->url_is_blocked($url)) {
            $this->check->add_successful(
                $title,
                $link,
                translation_manager::generate("error_httpsecurity", "coursechecker_links", $url)
            );
            return;
        }
        // Skip whitelisted domain.
        if ($this->is_ignored_domain($parseurl["host"])) {
            $this->check->add_successful(
                $title,
                $link,
                translation_manager::generate("domain_is_whitelisted", "coursechecker_links", $parseurl + ["url" => $url])
            );
            return;
        }
        // Skip whitelisted URL.
        if ($this->is_ignored_url($url)) {
            $this->check->add_successful(
                $title,
                $link,
                translation_manager::generate("url_is_whitelisted", "coursechecker_links", $parseurl + ["url" => $url])
            );
            return;
        }
        // Use curl to checks the urls.
        // You can use "$settings['debug'] = true;" to debug the curl request.
        $curl = new curl();

        $httpheader = [];
        $httpheader[] = "Accept-Encoding: gzip, deflate, br";
        $httpheader[] = "Accept:*/*";

        $curl->head($url, [
            "CURLOPT_HTTPHEADER" => $httpheader,
            "CURLOPT_CONNECTTIMEOUT" => $this->connecttimeout,
            "CURLOPT_TIMEOUT" => $this->timeout,
            "CURLOPT_FOLLOWLOCATION" => 1, // Follow redirects.
            "CURLOPT_MAXREDIRS" => 3, // Maximal number of redirects 301, 302.
            "CURLOPT_USERAGENT" => $this->useragent, // Default Moodle USERAGENT causing problems.
            "CURLOPT_SSL_VERIFYHOST" => 2,
            "CURLOPT_SSL_VERIFYPEER" => 1,
            "CURLOPT_ENCODING" => "gzip",
            "CURLOPT_REFERER" => $url, // Essentially this tells the server which page sent you there.
        ]);
        $curlinfo = $curl->get_info();

        $code = (int) $curlinfo["http_code"];
        if ($code === 0) {
            if ($this->file_get_content($title, $link, $url, $parseurl)) {
                return;
            }
            // Code 0: timeout or other curl error.
            $this->check->set('status', 'failed');
            $context = $parseurl + ["url" => $url, "curl_errno" => $curl->get_errno(), "curl_error" => $curl->error];
            $message = translation_manager::generate("error_curl", "coursechecker_links", $context);
            $this->check->add_failed($title, $link, $message);
            return;
        }

        $context = $parseurl + ["url" => $url, "http_code" => $code];
        if ($code >= 200 && $code < 400) {
            $message = translation_manager::generate("url_code_valid", "coursechecker_links", $context);
            $this->check->add_successful($title, $link, $message);
            return;
        }

        // If curl finds 404, we don't need to run file get content.
        if ($this->file_get_content($title, $link, $url, $parseurl) && $code != 404) {
            return;
        }

        // Code != 0 means it's a http error.
        $message = translation_manager::generate("error_code", "coursechecker_links", $context);
        $this->check->set('status', 'failed');
        $this->check->add_failed($title, $link, $message);
    }

    /**
     * Attempts to fetch a URL using PHP's file_get_contents as a fallback.
     *
     * @param string $title The title of the item being checked.
     * @param string $link The link to Moodle.
     * @param string $url The full URL to check.
     * @param array $parseurl Parsed URL components.
     * @return bool True if response is valid (200), false otherwise.
     */
    public function file_get_content(string $title, string $link, $url, $parseurl) {
        try {
            @file_get_contents($url);

            $httpresponse = null;
            if (!empty($http_response_header)) {
                $httpresponse = $this->parse_headers($http_response_header);
            }

            if (isset($httpresponse['reponse_code']) && (int) $httpresponse['reponse_code'] == 200) {
                $context = $parseurl + ["url" => $url, "http_code" => "200"];
                $message = translation_manager::generate("url_code_valid", "coursechecker_links", $context) . " (file_get_contents)";
                $this->check->add_successful($title, $link, $message);
                return true;
            }
        } catch (Exception $exception) {
            return false;
        }
        return false;
    }

    /**
     * Parses HTTP headers into a structured array.
     *
     * @param array $headers The headers returned from file_get_contents.
     * @return array Associative array of headers and response code.
     */
    protected function parse_headers($headers) {
        $head = [];
        foreach ($headers as $v) {
            $t = explode(':', $v, 2);
            if (isset($t[1])) {
                $head[trim($t[0])] = trim($t[1]);
            } else {
                $head[] = $v;
                if (preg_match("#HTTP/[0-9\.]+\s+([0-9]+)#", $v, $out)) {
                    $head['reponse_code'] = intval($out[1]);
                }
            }
        }
        return $head;
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

            // Normalize to lowercase
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
    protected function is_ignored_url(string $url) {
        foreach ($this->ignoredurls as $pattern) {
            // fnmatch erlaubt *, ? und andere Wildcards
            if (fnmatch($pattern, $url)) {
                return true;
            }
        }
        return false;
    }
}
