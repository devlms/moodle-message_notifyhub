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

namespace message_notifyhub\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;
use message_notifyhub\local\log;

/**
 * Privacy provider: the delivery log, kept in the system context.
 *
 * @package    message_notifyhub
 * @copyright  2026 DevLMS
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\core_userlist_provider,
    \core_privacy\local\request\plugin\provider {
    #[\Override]
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table(log::TABLE, [
            'userid' => 'privacy:metadata:log:userid',
            'component' => 'privacy:metadata:log:component',
            'name' => 'privacy:metadata:log:name',
            'channel' => 'privacy:metadata:log:channel',
            'status' => 'privacy:metadata:log:status',
            'timecreated' => 'privacy:metadata:log:timecreated',
        ], 'privacy:metadata:log');
        return $collection;
    }

    #[\Override]
    public static function get_contexts_for_userid(int $userid): contextlist {
        global $DB;
        $contextlist = new contextlist();
        if ($DB->record_exists(log::TABLE, ['userid' => $userid])) {
            $contextlist->add_system_context();
        }
        return $contextlist;
    }

    #[\Override]
    public static function get_users_in_context(userlist $userlist) {
        if ($userlist->get_context()->contextlevel != CONTEXT_SYSTEM) {
            return;
        }
        $userlist->add_from_sql('userid', 'SELECT userid FROM {' . log::TABLE . '}', []);
    }

    #[\Override]
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;
        $context = \context_system::instance();
        if (!in_array($context->id, $contextlist->get_contextids())) {
            return;
        }
        $rows = $DB->get_records(log::TABLE, ['userid' => $contextlist->get_user()->id], 'timecreated, id');
        if (!$rows) {
            return;
        }
        $deliveries = array_map(fn($row) => [
            'notification' => $row->component . '/' . $row->name,
            'channel' => $row->channel,
            'status' => $row->status,
            'time' => transform::datetime($row->timecreated),
        ], array_values($rows));
        writer::with_context($context)->export_data(
            [get_string('pluginname', 'message_notifyhub')],
            (object) ['deliveries' => $deliveries]
        );
    }

    #[\Override]
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;
        if ($context->contextlevel == CONTEXT_SYSTEM) {
            $DB->delete_records(log::TABLE);
        }
    }

    #[\Override]
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;
        if (in_array(\context_system::instance()->id, $contextlist->get_contextids())) {
            $DB->delete_records(log::TABLE, ['userid' => $contextlist->get_user()->id]);
        }
    }

    #[\Override]
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;
        if ($userlist->get_context()->contextlevel != CONTEXT_SYSTEM || !$userlist->get_userids()) {
            return;
        }
        [$in, $params] = $DB->get_in_or_equal($userlist->get_userids());
        $DB->delete_records_select(log::TABLE, "userid $in", $params);
    }
}
