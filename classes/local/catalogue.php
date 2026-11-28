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

namespace message_notifyhub\local;

/**
 * DevLMS channel plugins the hub can install: where each one's code lives on GitHub and what it reaches.
 *
 * Releases are not listed here: the newest release, its zip and its SHA-256 come from GitHub (see releases), so a
 * new channel release reaches every site without a new hub release. Any other message output or SMS gateway
 * installed on the site works in the hub too; this list is only what the hub can install for you.
 *
 * @package    message_notifyhub
 * @copyright  2026 DevLMS
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class catalogue {
    /** @var array[]|null For tests: entries instead of the built-in ones. */
    public static ?array $override = null;

    /** @var array[] component => entry; kind is message (an output) or sms (a core SMS gateway). */
    private const ENTRIES = [
        'message_ntfy' => [
            'kind' => 'message',
            'regions' => 'Worldwide',
            'repo' => 'devlms/moodle-message_ntfy',
            'guide' => 'https://devlms.com/work/moodle-notifications-on-your-phone-with-ntfy/',
        ],
        'message_webpush' => [
            'kind' => 'message',
            'regions' => 'Worldwide, in the browser',
            'repo' => 'devlms/moodle-message_webpush',
            'guide' => 'https://devlms.com/work/browser-notifications/',
        ],
        'smsgateway_africastalking' => [
            'kind' => 'sms',
            'regions' => 'Kenya, Uganda, Tanzania, Rwanda, Nigeria, Malawi, Zambia, Ethiopia and more',
            'repo' => 'devlms/moodle-smsgateway_africastalking',
            'guide' => 'https://devlms.com/work/moodle-sms-africas-talking/',
        ],
    ];

    /**
     * Every entry.
     *
     * @return array[]
     */
    public static function all(): array {
        return self::$override ?? self::ENTRIES;
    }

    /**
     * One entry.
     *
     * @param string $component
     * @return array|null
     */
    public static function get(string $component): ?array {
        return self::all()[$component] ?? null;
    }
}
