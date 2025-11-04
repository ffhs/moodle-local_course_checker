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

/**
 * This type of field should be used for config settings which contains a numeric value
 * which is within a minimum and maximum number range.
 *
 * @package    local_course_checker
 * @copyright  2025 Stefan Dani, Fernfachhochschule Schweiz (FFHS) <stefan.dani@ffhs.ch>
 * @copyright  based on work by 2019 Liip SA <elearning@liip.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_course_checker\admin;

use admin_setting_configtext;
use coding_exception;

/**
 * Class representing an admin setting for restricted integers.
 *
 * This setting enforces numeric validation, with optional minimum and maximum values.
 * It extends {@see admin_setting_configtext} to manage input as text.
 */
class admin_setting_restrictedint extends admin_setting_configtext {
    /**
     * @var int|null The maximum allowed value.
     */
    protected ?int $maximum = null;

    /**
     * @var int|null The minimum allowed value.
     */
    protected ?int $minimum = null;

    /**
     * @var bool Whether the field is required (cannot be empty).
     */
    protected bool $required = true;

    /**
     * Sets whether the field is required.
     *
     * @param bool $required True if required, false otherwise.
     */
    public function set_required(bool $required): void {
        $this->required = $required;
    }

    /**
     * Checks whether the field is required.
     *
     * @return bool True if required, false otherwise.
     */
    public function is_required(): bool {
        return $this->required;
    }

    /**
     * Validates the provided data.
     *
     * Ensures the value is an integer and within the defined range.
     *
     * {@inheritDoc}
     *
     * @param mixed $data The value to validate.
     * @return true|string True if valid, otherwise an error message.
     * @throws coding_exception
     */
    public function validate($data) {
        global $PAGE;

        $data = trim($data);

        // Don't force the plugin to be fully set up when installing. This is a Moodle behaviour.
        if ($PAGE->pagelayout === 'maintenance' && strlen($data) === 0) {
            return true;
        }

        // Allow empty value.
        if (!$this->required && empty($data)) {
            return true;
        }

        // Disallow empty value.
        if ($this->required && empty($data)) {
            return get_string('fieldrequired', 'error', $this->visiblename);
        }

        // Check that the value is an int.
        if (preg_match("/^[0-9]+$/", $data) !== 1) {
            return get_string("invalidadminsettingname", 'error', $this->visiblename);
        }

        if ($this->maximum !== null && $data > $this->maximum) {
            return get_string('admin_setting_restrictedint_max', 'local_course_checker', $this->maximum);
        }

        if ($this->minimum !== null && $data < $this->minimum) {
            return get_string('admin_setting_restrictedint_min', 'local_course_checker', $this->minimum);
        }

        return parent::validate($data);
    }

    /**
     * Sets the maximum value.
     *
     * @param int|null $maximum The maximum allowed value.
     * @return $this
     */
    public function set_maximum(?int $maximum = null): static {
        $this->maximum = $maximum;
        return $this;
    }

    /**
     * Sets the minimum value.
     *
     * @param int|null $minimum The minimum allowed value.
     * @return $this
     */
    public function set_minimum(?int $minimum = null): static {
        $this->minimum = $minimum;
        return $this;
    }
}
