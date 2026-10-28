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

namespace local_course_checker;

use coding_exception;

/**
 * Used to simplify the reading of config values
 *
 * @package    local_course_checker
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class config {
    /**
     * Get a configuration value.
     *
     * @param string $name Full configuration name in the form "plugin/setting".
     * @param mixed $defaultvalue Value returned when the setting has not been stored.
     * @return mixed
     * @throws coding_exception
     * @throws \dml_exception
     */
    public static function get(string $name, mixed $defaultvalue = null): mixed {
        [$plugin, $setting] = self::parse_name($name);
        $value = get_config($plugin, $setting);

        if ($value !== false) {
            return $value;
        }
        return $defaultvalue;
    }

    /**
     * Set a configuration value.
     *
     * Passing null removes the stored value, as defined by {@see set_config()}.
     *
     * @param string $name Full configuration name in the form "plugin/setting".
     * @param string|int|bool|null $value Value to store.
     * @return bool
     * @throws coding_exception
     */
    public static function set(string $name, string|int|bool|null $value): bool {
        [$plugin, $setting] = self::parse_name($name);

        return set_config($setting, $value, $plugin);
    }

    /**
     * Remove a stored configuration value so its default can be used again.
     *
     * @param string $name Full configuration name in the form "plugin/setting".
     * @return bool
     * @throws coding_exception
     */
    public static function reset(string $name): bool {
        [$plugin, $setting] = self::parse_name($name);

        return unset_config($setting, $plugin);
    }

    /**
     * Split and validate a full configuration name.
     *
     * @param string $name Full configuration name.
     * @return array{0: string, 1: string}
     * @throws coding_exception If the name is not in the expected format.
     */
    private static function parse_name(string $name): array {
        $parts = explode('/', $name);
        if (count($parts) !== 2 || $parts[0] === '' || $parts[1] === '') {
            throw new coding_exception('Configuration names must use the format "plugin/setting".');
        }

        return $parts;
    }
}
