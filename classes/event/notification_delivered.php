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

namespace message_notifyhub\event;

/**
 * The hub delivered a notification through one channel.
 *
 * @package    message_notifyhub
 * @copyright  2026 DevLMS
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @property-read array $other {
 *      - string component: component that sent the notification
 *      - string name: notification type
 *      - string channel: message output that delivered it
 * }
 */
class notification_delivered extends \core\event\base {
    /**
     * Build the event.
     *
     * @param int $userid Recipient.
     * @param string $component
     * @param string $name
     * @param string $channel
     * @return self
     */
    public static function for_user(int $userid, string $component, string $name, string $channel): self {
        return self::create([
            'context' => \context_system::instance(),
            'relateduserid' => $userid,
            'other' => ['component' => $component, 'name' => $name, 'channel' => $channel],
        ]);
    }

    #[\Override]
    protected function init() {
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    #[\Override]
    public static function get_name() {
        return get_string('event:delivered', 'message_notifyhub');
    }

    #[\Override]
    public function get_description() {
        return "The notification hub delivered the notification '{$this->other['component']}/{$this->other['name']}' " .
            "to the user with id '{$this->relateduserid}' through '{$this->other['channel']}'.";
    }

    #[\Override]
    public function get_url() {
        return new \moodle_url('/message/output/notifyhub/index.php');
    }

    #[\Override]
    protected function validate_data() {
        parent::validate_data();
        if (!isset($this->relateduserid)) {
            throw new \coding_exception('The \'relateduserid\' must be set.');
        }
        foreach (['component', 'name', 'channel'] as $key) {
            if (!isset($this->other[$key])) {
                throw new \coding_exception("The '$key' value must be set in other.");
            }
        }
    }

    #[\Override]
    public static function get_other_mapping() {
        return false;
    }
}
