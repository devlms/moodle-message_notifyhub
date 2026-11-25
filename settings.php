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
 * Settings, and the hub page under Messaging.
 *
 * @package    message_notifyhub
 * @copyright  2026 DevLMS
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// The hub page is listed even while the output is off, so admins can install channels first.
$ADMIN->add('messaging', new admin_externalpage(
    'message_notifyhub_hub',
    get_string('hubpage', 'message_notifyhub'),
    new moodle_url('/message/output/notifyhub/index.php')
));

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_heading(
        'message_notifyhub/intro',
        '',
        get_string('settingsintro', 'message_notifyhub', (new moodle_url('/message/output/notifyhub/index.php'))->out())
    ));
    $settings->add(new admin_setting_configtext(
        'message_notifyhub/keepdays',
        get_string('keepdays', 'message_notifyhub'),
        get_string('keepdays_desc', 'message_notifyhub'),
        30,
        PARAM_INT
    ));
}
