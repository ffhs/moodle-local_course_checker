# Course Checker [![Moodle Plugin CI](https://github.com/ffhs/moodle-local_course_checker/actions/workflows/moodle-plugin-ci.yml/badge.svg?branch=MOODLE_404_STABLE)](https://github.com/ffhs/moodle-local_course_checker/actions/workflows/moodle-plugin-ci.yml)

> [!CAUTION]
> Moodle 4.4 and 4.5 have reached end of life. Therefore, feature development for this plugin has stopped, and this is its final version. Only essential bug fixes and security fixes will be provided if needed.


## A Moodle course checker plugin that improves the quality and eliminate human routine tasks in online courses
This plugin provides a framework that can check a course based on independent checkers. Each checker is an independent [subplugin](https://docs.moodle.org/dev/Subplugins). It will help you find misconfiguration in your courses and follow your internal guidelines by displaying a structured report. The checkers can be triggered manually and will be executed by the Moodle AdHoc task system. A check can also be triggered via the companion plugin [block_course_checker_info](https://moodle.org/plugins/block_course_checker_info).

> [!NOTE]
> This plugin is the official replacement for [block_course_checker](https://moodle.org/plugins/block_course_checker). When first installed, this plugin will import settings from [block_course_checker](https://moodle.org/plugins/block_course_checker). If the import of the settings is not wanted/needed, you can uninstall [block_course_checker](https://moodle.org/plugins/block_course_checker) before installing the [local_course_checker](https://moodle.org/plugins/local_course_checker).

---

## 🚀 Features

- Imports and adapts settings from the deprecated `block_course_checker` plugin during installation.
- Runs all enabled checks or an individual check manually, with support for triggering checks from the companion `block_course_checker_info` plugin.
- Executes checks in parallel using Moodle adhoc tasks and reports their running, successful, informational, warning, failed, and error states.
- Provides an administration interface for enabling, disabling, and configuring independent checker subplugins.
- Uses separate Moodle capabilities for viewing reports, running checks, and managing saved report versions.
- Supports comparison-based checks against a configurable reference course.
- Is easily extended through [subplugins](https://docs.moodle.org/dev/Subplugins) (`coursechecker_xyz`), including custom result data, renderers, and Mustache templates.
- Displays structured, accordion-based reports with resolution links and a log of course changes recorded since the last check.
- Notifies the user through Moodle messaging when the requested checks complete or fail.
- Supports named report versions with configurable retention, deletion controls, and scheduled cleanup of obsolete versions and orphaned records.

---

## 🧪 Available Checkers

| **Checker** | **Integrated** | **Requires** | **Description** |  
|-------------|:--------------:|:------------:|-----------------|  
| [**coursechecker_attendance**](https://github.com/ffhs/moodle-coursechecker_attendance) | No, because of mod_attendance dependecy. | [mod_attendance](https://moodle.org/plugins/mod_attendance) | This check verifies that a course contains exactly one visible and viewable attendance activity and that no attendance sessions are configured. |  
| **coursechecker_activedates** | Yes | Nothing | This check examines visible, viewable activities of the configured types and reports enabled completion dates or activity-specific availability and deadline fields. Courses excluded by the configured course-name pattern, unconfigured activity types, and hidden or non-viewable activities are not checked. |
| **coursechecker_blocks** | Yes | Nothing | This check compares the presence of each configured block in the current course with its presence in the reference course and reports blocks that exist in only one of them. Blocks absent from both courses are ignored, and block configuration, position, and region are not compared. |
| **coursechecker_data** | Yes | Nothing | This check examines all visible and accessible database activities in a course. Each database activity must contain at least one field. Hidden or inaccessible database activities are not checked. |
| **coursechecker_groups** | Yes | Nothing | This check examines every user-visible assignment that has a view page. Assignments without group submission pass; when group submission is enabled, a valid grouping containing at least two groups is required. Hidden or otherwise inaccessible assignments are not checked. |
| **coursechecker_links** | Yes | PHP extension curl | This check extracts HTTP and HTTPS links from the course summary, activity names, introductions and content, book chapters and wiki pages, and directly checks URL activities for reachability and configured HTML error-page patterns. Exact URLs and domains on the configured whitelists are skipped, as are requests blocked by Moodle security policies. |
| **coursechecker_quiz** | Yes | Nothing | This check compares the maximum grade with the total question marks for every quiz in the course. Quizzes whose names match the configured whitelist regular expression are not compared. |
| **coursechecker_referencesettings** | Yes | Nothing | This check compares the selected general course settings with a reference course and can additionally compare course-level filter states and course-format options. Only configured setting groups are checked; unavailable selected course properties are reported as failures. |
| **coursechecker_rubrics_settings** | Yes | gradingform_rubric | This check examines assignments for which a rubric is enabled as the advanced grading method. The grade type must be set to points rather than a scale. An active rubric grading form must also be available. If the form is missing or still in draft status, a warning is issued. If the maximum rubric score does not match the maximum grade in the assignment settings, an error is reported. Assignments without an active rubric are not checked. |
| **coursechecker_subheadings** | Yes | Nothing | This check examines visible Text and media area activities and requires their first content element to be an <code>h4</code> heading containing a Font Awesome icon token. Other activity types and hidden activities are ignored, and content containing a configured whitelist string is accepted without these criteria. |
| **coursechecker_submission_feedbacktype** | Yes | mod_assign | This check examines assignments with an enabled Online text, File submissions, or Kaltura video submission type and requires at least one enabled feedback type. Assignments without any of these three submission types are ignored; other enabled submission types alone do not trigger the check.|
| **coursechecker_userdata** | Yes | Nothing | This check searches visible, viewable activities of the configured supported types for existing database, glossary, wiki, or forum content. It runs only for courses matching the configured course pattern; unsupported or disabled types are ignored, and activities whose names match the configured regular-expression whitelist are skipped. |

---

## ⚙️ Requirements

- Moodle **4.4 or higher**
- PHP **8.1 or higher**
- A working **cron job**
- PHP extension `curl` (required for the Broken Links checker)

---

## 📁Installing via uploaded ZIP file ##

1. Log in to your Moodle site as an admin and go to **Site administration > Plugins > Install plugins**.
2. Upload the ZIP file with the plugin code. You should only be prompted to add extra details if your plugin type is not automatically detected.
3. Check the plugin validation report and finish the installation.

## 📁Installing manually ##

The plugin can be also installed by putting the contents of this directory to

    {your/moodle/dirroot}/local/course_checker

Afterwards, log in to your Moodle site as an admin and go to **Site administration > Notifications_ to complete the installation.**

Alternatively, you can run

```bash
php admin/cli/upgrade.php
```

to complete the installation from the command line.

## 📦 Installing via GitHub

Clone the plugin into your Moodle instance:

```bash
cd /path/to/moodle
git clone https://github.com/ffhs/moodle-local_course_checker.git local/course_checker
```

Run the upgrade script:

```bash
php admin/cli/upgrade.php
```

Or complete the installation via the Moodle web interface: **Site administration > Notifications**

---

## ⚙️ For Developers

- Have a look at our [Developer Guide](README_DEV.md).
- Have you created a subplugin others can benefit from?
  - Feel free to integrate it by linking it into the table above via a merge request.

---

## 🧠 Authors

**Simon Gisler**\
[simon.gisler@ffhs.ch](mailto:simon.gisler@ffhs.ch)\
<a href="https://www.ffhs.ch" target="_blank">Swiss Distance University of Applied Sciences (FFHS)</a>

**Stefan Dani**\
[stefan.dani@ffhs.ch](mailto:stefan.dani@ffhs.ch)\
<a href="https://www.ffhs.ch" target="_blank">Swiss Distance University of Applied Sciences (FFHS)</a>

---

## 📝 License

This plugin is licensed under the [GNU GPL v3](https://www.gnu.org/licenses/gpl-3.0.html).
