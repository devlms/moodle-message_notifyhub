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

use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;
use message_notifyhub\local\log;
use message_notifyhub\privacy\provider;

/**
 * Privacy: a user's delivery rows are exported and deleted.
 *
 * @package    message_notifyhub
 * @copyright  2026 DevLMS
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \message_notifyhub\privacy\provider
 */
final class privacy_test extends \core_privacy\tests\provider_testcase {
    public function test_export_and_delete(): void {
        global $DB;
        $this->resetAfterTest();
        $a = $this->getDataGenerator()->create_user();
        $b = $this->getDataGenerator()->create_user();
        log::add($a->id, 'mod_assign', 'assign_notification', 'ntfy', log::SENT);
        log::add($b->id, 'mod_forum', 'posts', 'email', log::FAILED);
        $system = \context_system::instance();

        $this->assertEquals([$system->id], provider::get_contexts_for_userid($a->id)->get_contextids());
        $userlist = new userlist($system, 'message_notifyhub');
        provider::get_users_in_context($userlist);
        $this->assertEqualsCanonicalizing([$a->id, $b->id], $userlist->get_userids());

        provider::export_user_data(new approved_contextlist($a, 'message_notifyhub', [$system->id]));
        $data = writer::with_context($system)->get_data([get_string('pluginname', 'message_notifyhub')]);
        $this->assertSame('ntfy', $data->deliveries[0]['channel']);

        provider::delete_data_for_user(new approved_contextlist($a, 'message_notifyhub', [$system->id]));
        $this->assertSame(0, $DB->count_records(log::TABLE, ['userid' => $a->id]));
        $this->assertSame(1, $DB->count_records(log::TABLE, ['userid' => $b->id]));

        provider::delete_data_for_users(new approved_userlist($system, 'message_notifyhub', [$b->id]));
        $this->assertSame(0, $DB->count_records(log::TABLE));

        log::add($a->id, 'mod_assign', 'assign_notification', 'ntfy', log::SENT);
        provider::delete_data_for_all_users_in_context($system);
        $this->assertSame(0, $DB->count_records(log::TABLE));
    }
}
