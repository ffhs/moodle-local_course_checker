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
 * Settings for checking the labels subheadings and the leading icons inside the course
 *
 * @package    checker_subheadings
 * @copyright  2025 Simon Gisler, Fernfachhochschule Schweiz (FFHS) <simon.gisler@ffhs.ch>
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @copyright  based on work by 2019 Liip SA <elearning@liip.ch>
 * @copyright  based on work by 2019 Christoph Karlen, Fernfachhochschule Schweiz (FFHS) <christoph.karlen@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use checker_subheadings\checker;

defined('MOODLE_INTERNAL') || die();


/** @var admin_settingpage $settings */
$settings;

// Subheadings Checker Whitelist setting.
$visiblename = get_string('checker_subheadings_setting_whitelist', 'checker_subheadings');
$description = new lang_string('checker_subheadings_setting_whitelist_help', 'checker_subheadings');
$domainwhitelist = new admin_setting_configtextarea(
    checker::WHITELIST_SETTING,
    $visiblename,
    $description,
    checker::WHITELIST_DEFAULT,
    PARAM_RAW,
    600
);
$settings->add($domainwhitelist);
