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

/**
 * Class translation_manager
 *
 * Translation structures can contain user-provided placeholder values. By default, {@see self::resolve()}
 * escapes those values with {@see s()} and returns HTML-safe output. Callers may disable escaping only
 * when the next output layer performs context-appropriate escaping, for example Mustache's {{value}} syntax.
 * An unescaped result must never be passed to raw Mustache output ({{{value}}}) or echoed directly.
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
     * Concatenate multiple translation strings or plain strings.
     *
     * @param array $inputs Array of translation strings or plain strings.
     * @return string JSON-encoded structure.
     */
    public static function concat(array $inputs): string {
        $data = [
            'type' => 'concat',
            'items' => $inputs,
        ];
        return json_encode($data);
    }

    /**
     * Append a suffix to a translation string or plain string.
     *
     * @param string $input Original translation string or plain string.
     * @param string $suffix Suffix to append.
     * @return string JSON-encoded structure.
     */
    public static function append(string $input, string $suffix): string {
        return self::concat([$input, $suffix]);
    }

    /**
     * Prepend a prefix to a translation string or plain string.
     *
     * @param string $input Original translation string or plain string.
     * @param string $prefix Prefix to prepend.
     * @return string JSON-encoded structure.
     */
    public static function prepend(string $input, string $prefix): string {
        return self::concat([$prefix, $input]);
    }

    /**
     * Resolve a stored translation string into a translated message.
     *
     * If the string is not valid JSON or missing required keys, it is returned as-is.
     * Supports single translations and concatenated structures. The selected escaping mode is applied
     * recursively to plain strings in translation placeholders and concatenated items.
     *
     * Escaping is enabled by default for backwards compatibility and safe direct HTML output. Set
     * `$escape` to false only when the returned value is immediately passed to an output layer that escapes
     * it for its destination context. For example, a value resolved without escaping may be rendered with
     * Mustache's escaped `{{title}}` syntax, but not with raw `{{{title}}}` syntax.
     *
     * @param string|null $input The stored JSON translation structure or plain text.
     * @param bool $escape Whether to escape plain values with {@see s()}. Defaults to true.
     * @return string Resolved translation. HTML-safe when `$escape` is true; otherwise unescaped.
     */
    public static function resolve(?string $input, bool $escape = true): string {
        if (is_null($input) || $input === '') {
            return '';
        }

        // Try to decode full input.
        $data = json_decode(trim($input), true);

        // On a recursive call, the input may be another translation structure or a user-provided value.
        if (!is_array($data)) {
            return $escape ? s($input) : $input;
        }

        // If it's a concatenation.
        if (($data['type'] ?? null) === 'concat' && is_array($data['items'] ?? null)) {
            $result = '';
            foreach ($data['items'] as $item) {
                $result .= self::resolve($item, $escape);
            }
            return $result;
        }

        // If valid JSON with "identifier", process normally.
        if (
            !empty($data['identifier']) &&
            array_key_exists('component', $data) &&
            array_key_exists('a', $data) &&
            array_key_exists('lazyload', $data)
        ) {
            // Recursively translate $a.
            if (is_string($data['a'] ?? null)) {
                $data['a'] = self::resolve($data['a'], $escape);
            }

            if (is_array($data['a'] ?? null)) {
                foreach ($data['a'] as $key => $value) {
                    if (is_string($value)) {
                        $data['a'][$key] = self::resolve($value, $escape);
                    }
                }
            }

            $a = $data['a'] ?? null;
            $identifier = $data['identifier'];
            $component = $data['component'] ?? '';
            $lazyload = $data['lazyload'] ?? false;

            return (string) get_string($identifier, $component, $a, $lazyload);
        }

        // Not a recognised translation structure: return it using the requested escaping mode.
        return $escape ? s($input) : $input;
    }
}
