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

/**
 * Message output: hands each notification to the router.
 *
 * @package    message_notifyhub
 * @copyright  2026 DevLMS
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/message/output/lib.php');

/**
 * The hub as a message output.
 */
class message_output_notifyhub extends message_output {
    #[\Override]
    public function send_message($message) {
        return \message_notifyhub\local\router::deliver($message);
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
