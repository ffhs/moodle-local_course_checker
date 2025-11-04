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
 * Interface containing the activity modnames in Moodle.
 *
 * @package    local_course_checker
 * @copyright  2025 Simon Gisler, Fernfachhochschule Schweiz (FFHS) <simon.gisler@ffhs.ch>
 * @copyright  based on work by 2019 Adrian Perez, Fernfachhochschule Schweiz (FFHS) <adrian.perez@ffhs.ch>
 * @copyright  based on work by 2020 Christoph Karlen, Fernfachhochschule Schweiz (FFHS) <christoph.karlen@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_course_checker;

/**
 * Interface mod_type_interface
 *
 * Defines constants for various module types available in Moodle.
 * These constants represent different activity and resource modules
 * that can be used within a Moodle course.
 *
 * @package    local_course_checker
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface mod_type_interface {
    /** @var string Module name for assignment activity in Moodle. */
    const MOD_TYPE_ASSIGN = 'assign';

    /** @var string Module name for attendance in Moodle. */
    const MOD_TYPE_ATTENDANCE = 'attendance';

    /** @var string Module name for book in Moodle. */
    const MOD_TYPE_BOOK = 'book';

    /** @var string Module name for chat in Moodle. */
    const MOD_TYPE_CHAT = 'chat';

    /** @var string Module name for choice in Moodle. */
    const MOD_TYPE_CHOICE = 'choice';

    /** @var string Module name for choicegroup in Moodle. */
    const MOD_TYPE_CHOICEGROUP = 'choicegroup';

    /** @var string Module name for database in Moodle. */
    const MOD_TYPE_DATA = 'data';

    /** @var string Module name for feedback in Moodle. */
    const MOD_TYPE_FEEDBACK = 'feedback';

    /** @var string Module name for folder in Moodle. */
    const MOD_TYPE_FOLDER = 'folder';

    /** @var string Module name for forum in Moodle. */
    const MOD_TYPE_FORUM = 'forum';

    /** @var string Module name for glossary in Moodle. */
    const MOD_TYPE_GLOSSARY = 'glossary';

    /** @var string Module name for IMS content package in Moodle. */
    const MOD_TYPE_IMSCP = 'imscp';

    /** @var string Module name for journal in Moodle. */
    const MOD_TYPE_JOURNAL = 'journal';

    /** @var string Module name for label in Moodle. */
    const MOD_TYPE_LABEL = 'label';

    /** @var string Module name for lesson in Moodle. */
    const MOD_TYPE_LESSON = 'lesson';

    /** @var string Module name for external tool (LTI) in Moodle. */
    const MOD_TYPE_LTI = 'lti';

    /** @var string Module name for page in Moodle. */
    const MOD_TYPE_PAGE = 'page';

    /** @var string Module name for questionnaire in Moodle. */
    const MOD_TYPE_QUESTIONNAIRE = 'questionnaire';

    /** @var string Module name for quiz in Moodle. */
    const MOD_TYPE_QUIZ = 'quiz';

    /** @var string Module name for resource in Moodle. */
    const MOD_TYPE_RESOURCE = 'resource';

    /** @var string Module name for URL in Moodle. */
    const MOD_TYPE_URL = 'url';

    /** @var string Module name for scheduler in Moodle. */
    const MOD_TYPE_SCHEDULER = 'scheduler';

    /** @var string Module name for SCORM package in Moodle. */
    const MOD_TYPE_SCORM = 'scorm';

    /** @var string Module name for survey in Moodle. */
    const MOD_TYPE_SURVEY = 'survey';

    /** @var string Module name for wiki in Moodle. */
    const MOD_TYPE_WIKI = 'wiki';

    /** @var string Module name for workshop in Moodle. */
    const MOD_TYPE_WORKSHOP = 'workshop';
}
