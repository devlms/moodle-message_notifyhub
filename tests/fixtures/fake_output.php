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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace message_notifyhub;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/message/output/lib.php');

/**
 * A channel for tests: answers as told and remembers what it was given.
 *
 * @package    message_notifyhub
 * @copyright  2026 DevLMS
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class fake_output extends \message_output {
    /** @var \stdClass[] Messages it was asked to send. */
    public array $sent = [];

    /**
     * A fake channel.
     *
     * @param bool $works Whether sending succeeds.
     * @param bool $configured Whether users have set it up.
     */
    public function __construct(
        /** @var bool Whether sending succeeds. */
        public bool $works = true,
        /** @var bool Whether users have set it up. */
        public bool $configured = true,
    ) {
    }

    #[\Override]
    public function send_message($message) {
        $this->sent[] = $message;
        return $this->works;
    }

    #[\Override]
    public function is_user_configured($user = null) {
        return $this->configured;
    }

    #[\Override]
    public function config_form($preferences) {
        return '';
    }

    #[\Override]
    public function process_form($form, &$preferences) {
    }

    #[\Override]
    public function load_data(&$preferences, $userid) {
    }
}
