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
 * Delivery attempts: who, which notification, which channel, and whether it went. Never the text, which can hold
 * sign-in codes.
 *
 * @package    message_notifyhub
 * @copyright  2026 DevLMS
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class log {
    /** @var string The channel confirmed delivery. */
    public const SENT = 'sent';
    /** @var string The channel was tried and did not deliver. */
    public const FAILED = 'failed';
    /** @var string No channel delivered it. */
    public const UNDELIVERED = 'undelivered';
    /** @var string Moodle could not send an email (any email, not only the hub's). */
    public const EMAILFAILED = 'emailfailed';
    /** @var string Table name. */
    public const TABLE = 'message_notifyhub_log';

    /**
     * Add a row.
     *
     * @param int $userid
     * @param string $component
     * @param string $name
     * @param string $channel
     * @param string $status
     * @param int|null $time
     */
    public static function add(
        int $userid,
        string $component,
        string $name,
        string $channel,
        string $status,
        ?int $time = null
    ): void {
        global $DB;
        $DB->insert_record(self::TABLE, (object) [
            'userid' => $userid,
            'component' => \core_text::substr($component, 0, 100),
            'name' => \core_text::substr($name, 0, 100),
            'channel' => \core_text::substr($channel, 0, 100),
            'status' => $status,
            'timecreated' => $time ?? time(),
        ]);
    }

    /**
     * Newest rows first.
     *
     * @param int $limit
     * @param string $channel Only this channel when set.
     * @param string $status Only this status when set.
     * @return \stdClass[]
     */
    public static function recent(int $limit, string $channel = '', string $status = ''): array {
        global $DB;
        $where = [];
        $params = [];
        if ($channel !== '') {
            $where['channel'] = $channel;
        }
        if ($status !== '') {
            $where['status'] = $status;
        }
        return $DB->get_records(self::TABLE, $where, 'timecreated DESC, id DESC', '*', 0, $limit);
    }

    /**
     * Counts per channel and status since a time.
     *
     * @param int $since
     * @return array channel => [status => count]
     */
    public static function counts(int $since): array {
        global $DB;
        $rows = $DB->get_recordset_sql(
            'SELECT channel, status, COUNT(1) AS n FROM {' . self::TABLE . '} WHERE timecreated >= ? GROUP BY channel, status',
            [$since]
        );
        $counts = [];
        foreach ($rows as $row) {
            $counts[$row->channel][$row->status] = (int) $row->n;
        }
        $rows->close();
        return $counts;
    }

    /**
     * Remove rows older than the retention setting.
     */
    public static function prune(): void {
        global $DB;
        $days = max(1, (int) (get_config('message_notifyhub', 'keepdays') ?: 30));
        $DB->delete_records_select(self::TABLE, 'timecreated < ?', [time() - $days * DAYSECS]);
    }
}
