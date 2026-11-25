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
 * SMS results per gateway, read from core_sms's own table (core routes SMS between gateways itself).
 *
 * @package    message_notifyhub
 * @copyright  2026 DevLMS
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class sms {
    /**
     * Sent, failed and pending counts per gateway since a time.
     *
     * @param int $since
     * @return array[] name, sent, failed, pending
     */
    public static function summary(int $since): array {
        global $DB;
        $sql = 'SELECT m.gatewayid, m.status, COUNT(1) AS n
                  FROM {sms_messages} m
                 WHERE m.timecreated >= ? AND m.gatewayid IS NOT NULL
              GROUP BY m.gatewayid, m.status';
        $counts = [];
        $rows = $DB->get_recordset_sql($sql, [$since]);
        foreach ($rows as $row) {
            $status = \core_sms\message_status::tryFrom((string) $row->status) ?? \core_sms\message_status::UNKNOWN;
            $bucket = $status->is_sent() ? 'sent' : ($status->is_failed() ? 'failed' : 'pending');
            $counts[$row->gatewayid][$bucket] = ($counts[$row->gatewayid][$bucket] ?? 0) + (int) $row->n;
        }
        $rows->close();
        if (!$counts) {
            return [];
        }
        $names = $DB->get_records_list('sms_gateways', 'id', array_keys($counts), 'name', 'id, name');
        $summary = [];
        foreach ($names as $id => $gateway) {
            $summary[] = [
                'name' => format_string($gateway->name),
                'sent' => $counts[$id]['sent'] ?? 0,
                'failed' => $counts[$id]['failed'] ?? 0,
                'pending' => $counts[$id]['pending'] ?? 0,
            ];
        }
        return $summary;
    }
}
