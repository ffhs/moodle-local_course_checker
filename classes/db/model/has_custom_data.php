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

namespace local_course_checker\db\model;

use coding_exception;
use JsonException;

/**
 * Shared JSON-backed custom_data handling for persistent models.
 *
 * This trait must only be used in classes that extend \core\persistent
 * and define a 'custom_data' property in define_properties().
 *
 * @package    local_course_checker
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait has_custom_data {
    /**
     * Validates and stores custom data as JSON.
     *
     * Developers must pass an array or null.
     *
     * @param array|null $value
     * @return void
     * @throws coding_exception
     */
    protected function set_custom_data(?array $value): void {
        if ($value === null) {
            $this->raw_set('custom_data', null);
            return;
        }

        // Prevent collisions with persistent properties.
        $reservedkeys = array_keys(static::define_properties());
        $invalid = array_intersect(array_keys($value), $reservedkeys);

        if (!empty($invalid)) {
            throw new coding_exception(
                'custom_data contains reserved keys: ' . implode(', ', $invalid)
            );
        }

        try {
            $json = json_encode($value, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new coding_exception('Could not encode custom_data as JSON: ' . $e->getMessage());
        }
        $this->raw_set('custom_data', $json);
    }

    /**
     * Returns the decoded custom data.
     *
     * @return array
     * @throws coding_exception
     */
    protected function get_custom_data(): array {
        $value = $this->raw_get('custom_data');

        if ($value === null || $value === '') {
            return [];
        }

        try {
            $decoded = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            // Fail safe: return empty array instead of breaking execution.
            return [];
        }

        return is_array($decoded) ? $decoded : [];
    }
}
