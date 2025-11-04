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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin strings are defined here.
 *
 * @package     local_course_checker
 * @category    string
 * @copyright   2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Kurs Checker';
$string['subplugintype_checker'] = 'Kurs Checker';
$string['subplugintype_checker_plural'] = 'Kurs Checkers';
$string['settings_name'] = 'Allgemeine Einstellungen für den Kurs Checker';
$string['plugin_enabled'] = '{$a} aktiviert.';
$string['plugin_disabled'] = '{$a} deaktiviert.';
$string['settings_general'] = '<p>Laden Sie die Seite neu, wenn Sie ein Plugin aktivieren oder deaktivieren.</p>';
$string['settings_referencecourseid'] = 'Referenzkurs-ID';
$string['course_checker:view'] = 'Kurs Checker ansehen';
$string['course_checker:runcheck'] = 'Kurs Checker ausführen';
$string['course_checker:view_navigation'] = 'Kurs Checker in Navigation Menu anzeigen';

// Notification.
$string['course_checker:view_notification'] = 'Benachrichtigungen des Kurs Checker anzeigen';
$string['messageprovider:checker_completed'] = 'Kurs Check abgeschlossen';
// String for messageprovider.
// Multiple checks.
$string['messageprovider_subject'] = 'Kurs Check im Kurs {$a} abgeschlossen';
$string['messageprovider_completed'] = 'Kurs Check abgeschlossen.';
// Only one checks.
$string['messageprovider_singlechecks_subject'] = 'Check {$a->checkername} im Kurs {$a->coursename} abgeschlossen';
$string['messageprovider_singlechecks_completed'] = 'Der Check {$a->checkername} wurde abgeschlossen.';
$string['messageprovider_result_label'] = 'Ergebnisse';
$string['messageprovider_greeting'] = 'Hallo';
$string['messageprovider_following_checks_completed'] = 'Folgende Checks wurden abgeschlossen:';
// Fail in check tasks.
$string['messageprovider:checker_failed'] = 'Kurs Check ist fehlgeschlagen';
$string['messageprovider_subject_failed'] = 'Bei der Ausführung des Kurs Checks ist ein Fehler aufgetreten.';
$string['messageprovider_failed_notification_html'] = 'Hallo {$a->firstname},<br><br>
leider konnte der Kurs-Checker für den Kurs <strong>{$a->coursename}</strong> nicht abgeschlossen werden.<br><br>
Bitte wende dich an den Administrator.';
$string['messageprovider_failed_notification_small'] = 'Kurs Check konnte nicht abgeschlossen werden.';

// Result page.
$string['checker_last_run'] = 'Letzte Ausführung: {$a}';
$string['checker_never_run'] = 'Nie';
$string['failed_checks'] = 'Fehlgeschlagen';
$string['error_checks'] = 'Fehler';
$string['warning_checks'] = 'Warnung';
$string['successful_checks'] = 'Erfolgreich';
$string['check_course'] = 'Diesen Kurs prüfen';
$string['check_course_in_progress'] = 'Kurs Check läuft...';
$string['changes_last_check'] = 'Änderungen seit der letzten Kurs Checker Ausführung: {$a}';
$string['save_results'] = 'Ergebnisse speichern'; // ToDo: Dies ist nur die Übersetzung, das Speichern ist noch nicht implementiert.

// Event log translations.
$string['activity'] = 'Aktivität';
$string['last_modified_activity'] = 'Seit der letzten Ausführung geänderte Aktivitäten';
$string['action'] = 'Aktion';
$string['course'] = 'Kurs';
$string['course_section'] = 'Abschnitt';
$string['user'] = 'Benutzer';
$string['timestamp'] = 'Zeitstempel';
$string['details'] = 'Details';
$string['created'] = 'Erstellt';
$string['updated'] = 'Aktualisiert';
$string['deleted'] = 'Gelöscht';
$string['unknown'] = 'Unbekannt';

// Custom Admin Settings.
$string['admin_setting_regex_invalidregex'] = 'Der eingegebene reguläre Ausdruck ist ungültig. Bitte überprüfen Sie die Syntax.';

$string['admin_setting_coursesregex'] = 'Regex-Filter für vollständigen Kursnamen';
$string['admin_setting_coursesregex_help'] =
        'Definieren Sie den regulären Ausdruck, um diesen Prüfer nur dort zuzulassen, wo der Kursname übereinstimmt.';
$string['admin_setting_coursesregex_skip_course'] = 'Kurs Checker hat diesen Kurs übersprungen';
$string['admin_setting_coursesregex_skip_course_desc'] = 'Regulärer Ausdruck stimmt nicht mit dem Kursnamen überein.';

$string['admin_setting_restrictedint_min'] = 'Minimalwert ist {$a}';
$string['admin_setting_restrictedint_max'] = 'Maximalwert ist {$a}';

// String specific for the resolution link helper.
$string['checker_links_activity'] = 'Aktivität: {$a->name} ({$a->modname})';
$string['groups_activity'] = 'Aktivität "{$a->name}"';

// Tasks.
$string['queue_check_task'] = 'Kurs Checker einreihen';
$string['run_checker_task'] = 'Kurs Checker ausführen';
$string['send_notification_task'] = 'Benachrichtigung senden';

// Privacy provider.
$string['privacy:metadata:local_course_checker_event'] = 'Speichert Ereignisse und Aktivitätsprotokolle des Kurs-Checkers.';
$string['privacy:metadata:userid'] = 'Die ID des Benutzers, der das Ereignis ausgelöst hat.';
$string['privacy:metadata:relateduserid'] = 'Von dem Ereignis betroffener Benutzer.';
$string['privacy:metadata:checker_id'] = 'Die ID des Prüfvorgangs.';
$string['privacy:metadata:action'] = 'Die durchgeführte Aktion (z. B. Erstellen, Aktualisieren, Löschen).';
$string['privacy:metadata:target'] = 'Das Zielobjekt (z. B. course_module).';
$string['privacy:metadata:objectid'] = 'Die ID des betroffenen Objekts.';
$string['privacy:metadata:other'] = 'Zusätzliche Ereignisdaten im JSON-Format.';
$string['privacy:metadata:timecreated'] = 'Der Zeitpunkt, zu dem das Ereignis erstellt wurde.';
