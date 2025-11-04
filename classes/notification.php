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

namespace local_course_checker;

use coding_exception;
use core\message\message;
use core_user;
use moodle_url;
use stdClass;

/**
 * Notification class.
 *
 * @package    local_course_checker
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @copyright  2025 Simon Gisler, Fernfachhochschule Schweiz (FFHS) <simon.gisler@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class notification {
    /**
     * Plain text message format.
     *
     * Use when the message is purely plain text with no formatting.
     */
    private const FORMAT_PLAIN = FORMAT_PLAIN;
    /**
     * HTML message format.
     *
     * Use when the message contains HTML tags and should be rendered as HTML.
     */
    private const FORMAT_HTML = FORMAT_HTML;

    /**
     * Send successful checker completion notification.
     *
     * @param StdClass $user The user to notify.
     * @param StdClass $course The course context.
     * @param StdClass $checks The checks performed.
     * @return string|bool Message ID on success, false on failure.
     */
    public static function successful(StdClass $user, StdClass $course, StdClass $checks): bool|string {
        $originallang = self::use_user_language($user);
        global $PAGE;

        // Get Moodle renderer for local_course_checker.
        $renderer = $PAGE->get_renderer('local_course_checker');

        $url = (new moodle_url('/local/course_checker/index.php', ['courseid' => $course->id]))->out(false);

        $checksnames = [];
        foreach ($checks as $check) {
            $checksnames[] = get_string('pluginname', 'checker_' . $check);
        }

        $data = [
                'coursename' => $course->fullname,
                'user' => $user->lastname,
                'checks' => $checksnames,
                'result_url' => $url,
                'checkername' => $checksnames[0],
        ];

        $html = $renderer->render_from_template('local_course_checker/notification', $data);
        $plaintext = strip_tags($html);

        if (count((array) $checks) === 1) {
            $subject = get_string('messageprovider_singlechecks_subject', 'local_course_checker', $data);
            $smallmessage = get_string('messageprovider_singlechecks_completed', 'local_course_checker', $data);
        } else {
            $subject = get_string('messageprovider_subject', 'local_course_checker', $course->fullname);
            $smallmessage = get_string('messageprovider_completed', 'local_course_checker');
        }

        return self::send_message_noreply_user(
            $user->id,
            $course->id,
            'checker_completed',
            $subject,
            $plaintext,
            $html,
            $smallmessage,
            $url,
            $originallang,
            self::FORMAT_HTML,
        );
    }

    /**
     * Send failed checker notification.
     *
     * @param StdClass $user The user to notify.
     * @param StdClass $course The course context.
     * @return string|bool Message ID on success, false on failure.
     */
    public static function failed(StdClass $user, StdClass $course): bool|string {
        $originallang = self::use_user_language($user);

        $url = (new moodle_url('/local/course_checker/index.php', ['courseid' => $course->id]))->out(false);

        $data = (object)[
            'firstname' => $user->firstname,
            'coursename' => $course->fullname,
        ];

        $html = get_string('messageprovider_failed_notification_html', 'local_course_checker', $data);
        $plaintext = strip_tags(str_replace("<br>", "\n", $html));
        $subject = get_string('messageprovider_subject_failed', 'local_course_checker');
        $smallmessage = get_string('messageprovider_failed_notification_small', 'local_course_checker');

        return self::send_message_noreply_user(
            $user->id,
            $course->id,
            'checker_completed',
            $subject,
            $plaintext,
            $html,
            $smallmessage,
            $url,
            $originallang,
            self::FORMAT_PLAIN,
        );
    }

    /**
     * Send a message using Moodle messaging API.
     * And then restores the default language after it prepared the whole message for sending.
     *
     * @param int $userto User ID of the recipient.
     * @param int $courseid Course ID.
     * @param string $messagetype Message provider type.
     * @param string $subject Subject of the message.
     * @param string $plaintext Plain text version of the message.
     * @param string $html HTML version of the message.
     * @param string $smallmessage Small message summary.
     * @param string $url Context URL.
     * @param string|null $originallang Original lang.
     * @param int|string $format Format of the message (FORMAT_PLAIN, FORMAT_HTML, FORMAT_MARKDOWN).
     * @return string|bool Message ID on success, false on failure.
     * @throws coding_exception
     */
    protected static function send_message_noreply_user(
        int $userto,
        int $courseid,
        string $messagetype,
        string $subject,
        string $plaintext,
        string $html,
        string $smallmessage,
        string $url,
        ?string $originallang,
        int|string $format = FORMAT_PLAIN,
    ): bool|string {

        $message = new message();
        $message->component = 'local_course_checker';
        $message->name = $messagetype;
        $message->userfrom = core_user::get_noreply_user();
        $message->courseid = $courseid;
        $message->userto = $userto;
        $message->subject = $subject;
        $message->fullmessage = $plaintext;
        $message->fullmessageformat = $format;
        $message->fullmessagehtml = $html;
        $message->smallmessage = $smallmessage;
        $message->notification = 1;
        $message->contexturl = $url;
        $message->contexturlname = get_string('messageprovider_result_label', 'local_course_checker');

        force_current_language($originallang);
        return message_send($message);
    }

    /**
     * Temporarily switches the current language to the user's preferred language.
     *
     * This function stores the original language, then forces Moodle to use the provided user's language.
     * It should be paired with a call to {@see force_current_language()} to restore the original afterwards.
     *
     * @param StdClass $user The user object, must contain a 'lang' property.
     * @return string|null The original language before switching, or null if unchanged.
     */
    protected static function use_user_language(StdClass $user): ?string {
        $originallang = current_language();
        force_current_language($user->lang ?? $originallang);
        return $originallang;
    }
}
