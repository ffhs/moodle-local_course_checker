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
 * Settings for checking links inside the course
 *
 * @package    checker_links
 * @copyright  2025 Simon Gisler, Fernfachhochschule Schweiz (FFHS) <simon.gisler@ffhs.ch>
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @copyright  based on work by 2019 Liip SA <elearning@liip.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use checker_links\curl_manager;
use local_course_checker\admin\admin_setting_linklist;
use local_course_checker\admin\admin_setting_restrictedint;

/** @var admin_settingpage $settings */
$settings;

// CURL Connect timeout setting.
$visiblename = get_string('connect_timeout_setting', 'checker_links');
$description = get_string('connect_timeout_setting_desc', 'checker_links');
$timeout = new admin_setting_restrictedint(
    curl_manager::CONNECT_TIMEOUT_SETTING,
    $visiblename,
    $description,
    curl_manager::CONNECT_TIMEOUT_DEFAULT
);
$timeout->set_maximum(300)->set_minimum(0);
$settings->add($timeout);

// CURL Timeout setting.
$visiblename = get_string('timeout_setting', 'checker_links');
$description = get_string('timeout_setting_desc', 'checker_links');
$timeout = new admin_setting_restrictedint(
    curl_manager::TIMEOUT_SETTING,
    $visiblename,
    $description,
    curl_manager::TIMEOUT_DEFAULT
);
$timeout->set_maximum(300)->set_minimum(0);
$settings->add($timeout);

// Link Checker Useragent setting.
$visiblename = get_string('useragent_setting', 'checker_links');
$description = get_string('useragent_setting_desc', 'checker_links');
$useragent = new admin_setting_configtext(
    curl_manager::USERAGENT_SETTING,
    $visiblename,
    $description,
    curl_manager::USERAGENT_DEFAULT,
    PARAM_TEXT
);
$settings->add($useragent);

// Link Checker Urlwhitelist setting.
$visiblename = get_string('url_whitelist_setting', 'checker_links');
$description = get_string('url_whitelist_setting_desc', 'checker_links');
$urlwhitelist = new admin_setting_linklist(
    curl_manager::URL_WHITELIST_SETTING,
    $visiblename,
    $description,
    curl_manager::URL_WHITELIST_DEFAULT,
    PARAM_RAW,
    600
);
$settings->add($urlwhitelist);

// Link Checker Domainwhitelist setting.
$visiblename = get_string('domain_whitelist_setting', 'checker_links');
$description = get_string('domain_whitelist_setting_desc', 'checker_links');
$domainwhitelist = new admin_setting_linklist(
    curl_manager::DOMAIN_WHITELIST_SETTING,
    $visiblename,
    $description,
    curl_manager::DOMAIN_WHITELIST_DEFAULT,
    PARAM_RAW,
    600
);
$settings->add($domainwhitelist);
