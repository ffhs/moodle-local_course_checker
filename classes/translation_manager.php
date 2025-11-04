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

/**
 * Class translation_manager
 *
 * @package    local_course_checker
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class translation_manager {
    /**
     * Generate a structured translation string to store in the database.
     *
     * @param string $identifier Language string identifier.
     * @param string $component Language file component (e.g. 'local_course_checker').
     * @param mixed|null $a Placeholder(s) for the language string (scalar or array).
     * @param bool $lazyload Optional flag if client-side rendering is needed (not used in resolving).
     * @return string JSON-encoded structure.
     */
    public static function generate(string $identifier, string $component = '', mixed $a = null, bool $lazyload = false): string {
        $data = [
            'identifier' => $identifier,
            'component' => $component,
            'a' => $a,
            'lazyload' => $lazyload,
        ];
        return json_encode($data);
    }

    /**
     * Resolve a stored translation string into a translated message.
     *
     * If the string is not valid JSON or missing required keys, it is returned as-is.
     *
     * @param string|null $input The stored JSON translation structure or plain text.
     * @return string Translated string, or fallback.
     */
    public static function resolve(?string $input): string {
        if (is_null($input) || trim($input) === '') {
            return '';
        }

        $input = trim($input);

        // Try to decode full input.
        $data = json_decode($input, true);

        // If valid JSON with "identifier", process normally.
        if (
            is_array($data) &&
            !empty($data['identifier']) &&
            array_key_exists('component', $data) &&
            array_key_exists('a', $data) &&
            array_key_exists('lazyload', $data)
        ) {
            // Recursively translate $a.
            if (is_string($data['a'] ?? null)) {
                $data['a'] = self::resolve($data['a']);
            }

            if (is_array($data['a'] ?? null)) {
                foreach ($data['a'] as $key => $value) {
                    if (is_string($value)) {
                        $data['a'][$key] = self::resolve($value);
                    }
                }
            }

            $a = $data['a'] ?? null;
            $identifier = $data['identifier'];
            $component = $data['component'] ?? '';
            $lazyload = $data['lazyload'] ?? false;

            return get_string($identifier, $component, $a, $lazyload);
        }

        // If the JSON is invalid or no identifier is included,
        // try to recognize multiple JSON objects in a row.
        preg_match_all('/\{(?:[^{}]|(?R))*\}/', $input, $matches);

        if (count($matches[0]) > 1) {
            $resolved = [];
            foreach ($matches[0] as $part) {
                $resolved[] = self::resolve($part);
            }
            return implode(' ', $resolved);
        }

        // Not a valid JSON with "identifier" -> return input as-is.
        return $input;
    }
}
