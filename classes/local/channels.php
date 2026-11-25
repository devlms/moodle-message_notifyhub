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
 * The channels the hub can use, in the admin's order.
 *
 * A channel is any message output that is on for the site, except popup (every notification is already kept for
 * the bell) and the hub itself. Channels missing from the saved order are added before email, so a new output is
 * tried before the catch-all.
 *
 * @package    message_notifyhub
 * @copyright  2026 DevLMS
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class channels {
    /** @var string[] Outputs the hub never routes to. */
    public const EXCLUDED = ['notifyhub', 'popup'];

    /**
     * Every usable output, keyed by name.
     *
     * @return \message_output[]
     */
    public static function outputs(): array {
        if (router::$outputs !== null) {
            return router::$outputs;
        }
        $outputs = [];
        foreach (get_message_processors(true) as $name => $processor) {
            if (!in_array($name, self::EXCLUDED, true) && !empty($processor->object)) {
                $outputs[$name] = $processor->object;
            }
        }
        return $outputs;
    }

    /**
     * Channel names in the order the hub tries them, including those switched off in the hub.
     *
     * @return string[]
     */
    public static function ordered(): array {
        $available = array_keys(self::outputs());
        $saved = array_values(array_intersect(self::split(get_config('message_notifyhub', 'order')), $available));
        $new = array_diff($available, $saved);
        sort($new);
        $emailat = array_search('email', $saved, true);
        if ($emailat === false) {
            // Email stays the last resort.
            $new = array_values(array_diff($new, ['email']));
            $order = array_merge($saved, $new);
            if (in_array('email', $available, true)) {
                $order[] = 'email';
            }
            return $order;
        }
        array_splice($saved, $emailat, 0, $new);
        return $saved;
    }

    /**
     * Channels the hub may use, in order.
     *
     * @return string[]
     */
    public static function active(): array {
        $off = self::split(get_config('message_notifyhub', 'off'));
        return array_values(array_diff(self::ordered(), $off));
    }

    /**
     * Whether a channel is switched off in the hub.
     *
     * @param string $name
     * @return bool
     */
    public static function is_off(string $name): bool {
        return in_array($name, self::split(get_config('message_notifyhub', 'off')), true);
    }

    /**
     * Switch a channel on or off in the hub.
     *
     * @param string $name
     * @param bool $on
     */
    public static function set_on(string $name, bool $on): void {
        $off = array_diff(self::split(get_config('message_notifyhub', 'off')), [$name]);
        if (!$on) {
            $off[] = $name;
        }
        set_config('off', implode(',', $off), 'message_notifyhub');
    }

    /**
     * Move a channel up (-1) or down (+1).
     *
     * @param string $name
     * @param int $step
     */
    public static function move(string $name, int $step): void {
        $order = self::ordered();
        $at = array_search($name, $order, true);
        $to = $at === false ? false : $at + ($step < 0 ? -1 : 1);
        if ($to === false || $to < 0 || $to >= count($order)) {
            return;
        }
        [$order[$at], $order[$to]] = [$order[$to], $order[$at]];
        set_config('order', implode(',', $order), 'message_notifyhub');
    }

    /**
     * Comma list to array.
     *
     * @param mixed $value
     * @return string[]
     */
    private static function split($value): array {
        return array_values(array_filter(array_map('trim', explode(',', (string) $value))));
    }
}
