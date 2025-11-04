# 📚 Moodle Course Checker
## A Moodle course checker plugin that improves the quality and eliminate human routine tasks in online courses
This plugin provides a framework that can check a course based on independent checkers. Each checker is an independent [subplugin](https://docs.moodle.org/dev/Subplugins). It will help you find misconfiguration in your courses and follow your internal guidelines by displaying a structured report. The checkers can be triggered manually an will be executed by the Moodle AdHoc task system. A check can also be triggered via the companion plugin [block_course_checker_info](https://moodle.org/plugins/plugin/block_course_checker_info).

> [!NOTE]
> This plugin is the official replacement for [block_course_checker](https://moodle.org/plugins/block_course_checker). When first installed, this plugin will import settings from [block_course_checker](https://moodle.org/plugins/block_course_checker). If the import of the settings is not wanted/needed, you can uninstall [block_course_checker](https://moodle.org/plugins/block_course_checker) before installing the [local_course_checker](https://moodle.org/plugins/local_course_checker).

---

## 🚀 Features

- Import and adapt settings from deprecated `block_course_checker`.
- Automatic checks of course configurations (e.g., activity names, due dates, completion).
- Easily extendable via [subplugins](https://docs.moodle.org/dev/Subplugins) (`checker_xyz`).
- Accordion-based display using Mustache templates and Bootstrap.
- Display a changes log since last check on top of the report.
- Adhoc tasks for parallel check execution.
- Notification when the course check is completed.

---

## 🧪 Available Checkers

<table>
  <thead>
    <tr>
      <th>Checker</th>
      <th>Integrated</th>
      <th>Requires</th>
      <th>Description</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><a href="https://moodle.org/plugins/checker_attendance">moodle-checker_attendance</a></td>
      <td>No, because of mod_attendance dependecy.</td>
      <td><a href="https://moodle.org/plugins/mod_attendance">mod_attendance</a></td>
      <td>This plugin checks whether a course contains exactly one visible attendance activity and that it does not contain any preconfigured sessions. It ensures consistent setup of attendance tracking across courses.</td>
    </tr>
    <tr>
      <td>moodle-checker_activedates</td>
      <td>Yes</td>
      <td>Nothing</td>
      <td>This plugin checks whether a course has both a <strong>start date</strong> and an <strong>end date</strong> defined. It is designed to be part of a course quality assurance workflow, ensuring that all courses have clearly set temporal boundaries.</td>
    </tr>
    <tr>
      <td>moodle-checker_blocks</td>
      <td>Yes</td>
      <td>Nothing</td>
      <td>This plugin compares the enabled blocks in a course against a reference course to ensure consistent block layout. It checks for missing or mismatched blocks and helps maintain a standardized course structure across the platform.</td>
    </tr>
    <tr>
      <td>moodle-checker_data</td>
      <td>Yes</td>
      <td>Nothing</td>
      <td>This plugin checks all database activities in a course to ensure they contain at least one defined field. It helps prevent incomplete configurations by flagging empty database modules.</td>
    </tr>
    <tr>
      <td>moodle-checker_groups</td>
      <td>Yes</td>
      <td>Nothing</td>
      <td>This plugin checks assignment activities in a course to ensure correct group submission configuration. It verifies that group mode is properly set, a valid grouping is assigned, and that the grouping contains at least two groups.</td>
    </tr>
    <tr>
      <td>moodle-checker_links</td>
      <td>Yes</td>
      <td>PHP extension <code>curl</code></td>
      <td>This plugin scans course content (including summaries, modules, books, wikis, and URLs) for hyperlinks and checks their validity. It helps identify broken or unreachable links to ensure a reliable learning experience.</td>
    </tr>
    <tr>
      <td>moodle-checker_quiz</td>
      <td>Yes</td>
      <td>Nothing</td>
      <td>This plugin checks whether each quiz's “Maximum grade” matches the “Total of marks” assigned to its questions. It helps prevent grading inconsistencies and ensures quizzes are correctly configured.</td>
    </tr>
    <tr>
      <td>moodle-checker_referencesettings</td>
      <td>Yes</td>
      <td>Nothing</td>
      <td>This plugin compares general course settings (such as category, format, language, filters, and format options) of the current course with those of a reference course. It ensures consistency across courses, which is especially useful in standardized learning environments.</td>
    </tr>
    <tr>
      <td>moodle-checker_subheadings</td>
      <td>Yes</td>
      <td>Nothing</td>
      <td>The Subheading Checker verifies that all **label resources** in a course follow consistent formatting standards. Specifically, it checks whether each label:
      <ul>
      <li>Begins with an <code>&lt;h4&gt;</code> heading (configurable in code).</li>
      <li>Includes a FontAwesome icon (e.g., <code>[icon fa-book]</code>) in the heading.</li>
      <li><strong>Is not whitelisted</strong> (certain labels can be excluded via configuration).</li>
      </ul>
      </td>
    </tr>
    <tr>
      <td>moodle-checker_userdata</td>
      <td>Yes</td>
      <td>Nothing</td>
      <td>Checks course activities for residual user data such as submissions, forum posts, or logs. Helps ensure that no personal user content is left in template or duplicated courses.</td>
    </tr>
  </tbody>
</table>

---

## ⚙️ Requirements

- Moodle **5.0 or higher**
- PHP **8.3 or higher**
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

[Developer Guide](README_DEV.md).

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
