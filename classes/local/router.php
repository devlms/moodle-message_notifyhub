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

use message_notifyhub\event\notification_delivered;
use message_notifyhub\event\notification_undelivered;

/**
 * Sends one notification to one user through the first channel that works.
 *
 * Core calls every output a user has switched on and ignores what they answer, so it cannot fall back. The hub
 * is itself an output: it tries the channels in the admin's order and stops at the first that confirms delivery.
 * It skips channels the user has not set up and channels that already get this notification for this user, so
 * nobody gets the same notification twice.
 *
 * @package    message_notifyhub
 * @copyright  2026 DevLMS
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class router {
    /** @var \message_output[]|null For tests: channel name => output, instead of the site's outputs. */
    public static ?array $outputs = null;

    /**
     * Deliver a notification.
     *
     * @param \stdClass $message As core hands it to an output.
     * @return bool False only when channels were tried and none delivered.
     */
    public static function deliver(\stdClass $message): bool {
        $user = is_object($message->userto) ? $message->userto : \core_user::get_user($message->userto);
        if (!$user || !empty($user->deleted)) {
            return false;
        }
        $component = (string) ($message->component ?? '');
        $name = (string) ($message->name ?? '');
        $outputs = channels::outputs();
        $tried = [];
        foreach (channels::active() as $channel) {
            $output = $outputs[$channel] ?? null;
            if (!$output || !$output->is_user_configured($user)) {
                continue;
            }
            if (self::already_receives($channel, $output, $user, $component, $name)) {
                // The user gets this notification on that channel anyway: the hub adds nothing.
                return true;
            }
            $tried[] = $channel;
            if (self::send($output, $message)) {
                log::add($user->id, $component, $name, $channel, log::SENT);
                notification_delivered::for_user($user->id, $component, $name, $channel)->trigger();
                return true;
            }
            log::add($user->id, $component, $name, $channel, log::FAILED);
        }
        log::add($user->id, $component, $name, '', log::UNDELIVERED);
        notification_undelivered::for_user($user->id, $component, $name, $tried)->trigger();
        return false;
    }

    /**
     * Send a test notification to the current user through one channel.
     *
     * @param string $channel
     * @return bool
     */
    public static function send_test(string $channel): bool {
        global $USER;
        $output = channels::outputs()[$channel] ?? null;
        if (!$output) {
            return false;
        }
        $site = format_string(get_site()->fullname, true, ['context' => \context_system::instance()]);
        $text = get_string('test:body', 'message_notifyhub', $site);
        $message = (object) [
            'userto' => $USER,
            'userfrom' => \core_user::get_noreply_user(),
            'component' => 'message_notifyhub',
            'name' => 'test',
            'subject' => get_string('test:subject', 'message_notifyhub'),
            'fullmessage' => $text,
            'fullmessageformat' => FORMAT_PLAIN,
            'fullmessagehtml' => '<p>' . s($text) . '</p>',
            'smallmessage' => $text,
            'notification' => 1,
            'contexturl' => (new \moodle_url('/message/output/notifyhub/index.php'))->out(false),
            'contexturlname' => get_string('pluginname', 'message_notifyhub'),
        ];
        $ok = self::send($output, $message);
        log::add($USER->id, 'message_notifyhub', 'test', $channel, $ok ? log::SENT : log::FAILED);
        return $ok;
    }

    /**
     * Call one output, treating an exception as a failure.
     *
     * @param \message_output $output
     * @param \stdClass $message
     * @return bool
     */
    private static function send(\message_output $output, \stdClass $message): bool {
        try {
            return (bool) $output->send_message(clone $message);
        } catch (\Throwable $e) {
            debugging('Notification hub: ' . $e->getMessage(), DEBUG_DEVELOPER);
            return false;
        }
    }

    /**
     * Whether core already sends this notification to this user on a channel, using the same rules as
     * \core\message\manager: a locked setting wins; otherwise the user's preference, or the site default.
     *
     * @param string $channel
     * @param \message_output $output
     * @param \stdClass $user
     * @param string $component
     * @param string $name
     * @return bool
     */
    public static function already_receives(
        string $channel,
        \message_output $output,
        \stdClass $user,
        string $component,
        string $name
    ): bool {
        $base = $component . '_' . $name;
        $enabled = 'message_provider_' . $base . '_enabled';
        $defaults = get_message_output_default_preferences();
        if (!empty($defaults->{$channel . '_provider_' . $base . '_locked'})) {
            return in_array($channel, explode(',', (string) ($defaults->{$enabled} ?? '')), true);
        }
        if (!empty($user->emailstop)) {
            return false;
        }
        $list = get_user_preferences($enabled, null, $user) ?: (string) ($defaults->{$enabled} ?? '');
        return in_array($channel, explode(',', $list), true);
    }
}
