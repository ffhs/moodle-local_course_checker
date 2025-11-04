# 📚 Checker Subheadings for Moodle: Subplugin of the Local Course Checker Plugin

The Subheading Checker verifies that all **label resources** in a course follow consistent formatting standards. Specifically, it checks whether each label:

- Begins with an `<h4>` heading (configurable in code).
- Includes a FontAwesome icon (e.g., `[icon fa-book]`) in the heading.
- Is **not whitelisted** (certain labels can be excluded via configuration).

---

## ⚙️ Requirements

- Moodle **4.0 or higher**
- A working **cron job**
- PHP extension `curl` (required for the Broken Links checker)

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
