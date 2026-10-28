# Changelog for local_course_checker

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [v5.0-r2] (Build:2026090100), [v4.4-r2] (Build:2026083100) - 2026-09-01

### Added

- New states for checks: `info` (blue) and `running` (grey).
- Help icons for check states.
- Support for custom check/result data and overriding Mustache templates and renderers from within subplugins.
- String concatenation support in `translation_manager` to maintain translatability.
- **Link Checker:** Regex settings to detect "soft 404s" (sites returning 200 OK for error pages).
- **Link Checker:** File type icons (zip, html, pdf, image) in the report view.
- **Quiz Checker:** Added regex whitelist.
- **User data check:** Added regex whitelist for forum activities.
- **Rubrics settings check:** Added new check for rubrics settings. Checks assignments for correct rubric settings and if the points match the assignment settings.
- **Submission feedbacktype check:** Added new check for submission feedbacktype settings. This plugin checks if a submission has set a feedbacktype.
- Added 🐧
- Report versioning: Users are now able to save old version of the report.
- Added scheduled cleanup task to remove old reports of inexistent courses.

### Fixed

- Error messages no longer accumulate indefinitely when a check fails repeatedly.
- Database records are now properly cleaned up when rerunning a check.
- **Link Checker:** Fixed a bug where the whitelist was being ignored.

### Changed

- **Database Schema:**
    - Added `custom_data` field to `check` and `check_result` tables.
    - Changed default value of `status` field from `successful` to `error`.
- **Link Checker:** Refactored specific logic out of the main plugin into the subplugin.

## [v5.0-r1] (Build:2025111900) - 2025-11-19

- First release of the Local Course Checker plugin for Moodle 5.

## [v4.4-r1] (Build:2025111800) - 2025-11-18

- First release of the Local Course Checker plugin for Moodle 4.4.
