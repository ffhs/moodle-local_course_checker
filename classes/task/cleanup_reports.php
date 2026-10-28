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

namespace local_course_checker\task;

use core\task\scheduled_task;
use local_course_checker\config;
use local_course_checker\db\model\checker;
use local_course_checker\output\report_renderer;

/**
 * Cleans orphaned records and reports which are no longer needed.
 *
 * @package    local_course_checker
 * @copyright  2026 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class cleanup_reports extends scheduled_task {
    /**
     * {@inheritDoc}
     */
    public function get_name(): string {
        return get_string('cleanup_reports_task', 'local_course_checker');
    }

    /**
     * {@inheritDoc}
     */
    public function execute(): void {
        global $DB;

        mtrace('Cleaning orphaned Course Checker records.');
        $sql = 'checker_id NOT IN (SELECT id FROM {local_course_checker})';
        $DB->delete_records_select('local_course_checker_check', $sql);
        $DB->delete_records_select('local_course_checker_event', $sql);

        $sql = 'check_id NOT IN (SELECT id FROM {local_course_checker_check})';
        $DB->delete_records_select('local_course_checker_check_result', $sql);
        mtrace('Orphaned record cleanup completed.');

        if (config::get(report_renderer::ENABLE_VERSIONING, report_renderer::ENABLE_VERSIONING_DEFAULT)) {
            $maxsavedreports = (int) config::get(report_renderer::MAXSAVEDREPORTS, report_renderer::MAXSAVEDREPORTS_DEFAULT);
            if ($maxsavedreports <= 0) {
                mtrace('Report versioning is enabled with no saved-report limit.');
                return;
            }

            $sql = "SELECT course_id
                      FROM {local_course_checker}
                     WHERE version_name <> :latest
                  GROUP BY course_id
                    HAVING COUNT(id) > :maxsavedreports";
            $courseids = $DB->get_fieldset_sql($sql, [
                'latest' => 'latest',
                'maxsavedreports' => $maxsavedreports,
            ]);
            if (!$courseids) {
                mtrace('All courses are within the saved-report limit.');
                return;
            }

            [$coursesql, $courseparams] = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED, 'course');
            $reports = checker::get_records_select(
                "course_id $coursesql AND version_name <> :latest",
                $courseparams + ['latest' => 'latest'],
                'course_id ASC, timestamp DESC, id DESC',
            );

            $savedreportcounts = [];
            $transaction = $DB->start_delegated_transaction();
            foreach ($reports as $report) {
                $courseid = $report->get('course_id');
                $savedreportcounts[$courseid] = ($savedreportcounts[$courseid] ?? 0) + 1;
                if ($savedreportcounts[$courseid] > $maxsavedreports) {
                    $report->delete();
                    mtrace("Deleted an excess saved report for course $courseid.");
                }
            }
            $transaction->allow_commit();
            mtrace('Saved-report limit cleanup completed.');
            return;
        }

        $hasnamedversions = $DB->record_exists_select(
            checker::TABLE,
            'version_name <> :latest',
            ['latest' => 'latest'],
        );

        if (!$hasnamedversions) {
            mtrace('All reports are already named latest; no report versions need to be removed.');
            return;
        }

        mtrace('Report versioning is disabled; retaining only the newest report for each course.');
        $reports = checker::get_records([], 'timestamp DESC, id', 'DESC');
        $newestbycourse = [];
        $transaction = $DB->start_delegated_transaction();

        foreach ($reports as $report) {
            $courseid = $report->get('course_id');
            if (!isset($newestbycourse[$courseid])) {
                $newestbycourse[$courseid] = $report;
                continue;
            }

            $report->delete();
        }

        foreach ($newestbycourse as $courseid => $report) {
            if ($report->get('version_name') !== 'latest') {
                $report->set('version_name', 'latest');
                $report->update();
                mtrace("Renamed newest report for course $courseid to latest.");
            }
        }

        $transaction->allow_commit();
        mtrace('Obsolete report versions were removed.');
    }
}
