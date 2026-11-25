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

use message_notifyhub\local\channels;
use message_notifyhub\local\log;
use message_notifyhub\local\router;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/fixtures/fake_output.php');

/**
 * Routing: one copy per user, on the first channel that delivers, in the admin's order.
 *
 * @package    message_notifyhub
 * @copyright  2026 DevLMS
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \message_notifyhub\local\router
 * @covers     \message_notifyhub\local\channels
 * @covers     \message_notifyhub\local\log
 */
final class router_test extends \advanced_testcase {
    #[\Override]
    protected function tearDown(): void {
        router::$outputs = null;
        parent::tearDown();
    }

    /**
     * A notification as core hands it to an output.
     *
     * @param \stdClass $user
     * @return \stdClass
     */
    private function message(\stdClass $user): \stdClass {
        return (object) [
            'userto' => $user,
            'userfrom' => \core_user::get_noreply_user(),
            'component' => 'mod_assign',
            'name' => 'assign_notification',
            'subject' => 'Your essay was graded',
            'fullmessage' => 'Secret code 123456',
            'fullmessageformat' => FORMAT_PLAIN,
            'fullmessagehtml' => '',
            'smallmessage' => 'Graded',
            'notification' => 1,
        ];
    }

    public function test_the_first_channel_that_delivers_wins(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $first = new fake_output();
        $second = new fake_output();
        router::$outputs = ['first' => $first, 'second' => $second];
        set_config('order', 'first,second', 'message_notifyhub');

        $this->assertTrue(router::deliver($this->message($user)));
        $this->assertCount(1, $first->sent);
        $this->assertCount(0, $second->sent, 'One copy only.');
    }

    public function test_a_failing_channel_falls_back_to_the_next(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $broken = new fake_output(works: false);
        $backup = new fake_output();
        router::$outputs = ['broken' => $broken, 'backup' => $backup];
        set_config('order', 'broken,backup', 'message_notifyhub');
        $sink = $this->redirectEvents();

        $this->assertTrue(router::deliver($this->message($user)));
        $this->assertCount(1, $backup->sent);
        $rows = array_values(log::recent(10));
        $this->assertSame(['sent', 'failed'], array_column($rows, 'status'), 'Newest first: the failure, then the delivery.');
        $this->assertSame('backup', $rows[0]->channel);
        $delivered = array_filter($sink->get_events(), fn($e) => $e instanceof event\notification_delivered);
        $this->assertCount(1, $delivered);
        $this->assertSame('backup', reset($delivered)->other['channel']);
    }

    public function test_channels_the_user_has_not_set_up_are_skipped(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $unset = new fake_output(configured: false);
        $backup = new fake_output();
        router::$outputs = ['unset' => $unset, 'backup' => $backup];
        set_config('order', 'unset,backup', 'message_notifyhub');

        router::deliver($this->message($user));
        $this->assertCount(0, $unset->sent);
        $this->assertCount(1, $backup->sent);
    }

    public function test_a_channel_that_already_gets_this_notification_is_not_sent_a_second_copy(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $email = new fake_output();
        $ntfy = new fake_output();
        router::$outputs = ['email' => $email, 'ntfy' => $ntfy];
        set_config('order', 'email,ntfy', 'message_notifyhub');
        // The user gets assignment notifications by email and through the hub.
        set_user_preference('message_provider_mod_assign_assign_notification_enabled', 'email,notifyhub', $user);

        $this->assertTrue(router::deliver($this->message($user)));
        $this->assertCount(0, $email->sent, 'Core already emails this user.');
        $this->assertCount(0, $ntfy->sent, 'The user already has it, so the hub sends nothing.');
    }

    public function test_when_every_channel_fails_the_notification_is_undelivered(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        router::$outputs = ['a' => new fake_output(works: false), 'b' => new fake_output(works: false)];
        set_config('order', 'a,b', 'message_notifyhub');
        $sink = $this->redirectEvents();

        $this->assertFalse(router::deliver($this->message($user)));
        $undelivered = array_filter($sink->get_events(), fn($e) => $e instanceof event\notification_undelivered);
        $this->assertCount(1, $undelivered);
        $this->assertEquals($user->id, reset($undelivered)->relateduserid);
    }

    public function test_channels_switched_off_in_the_hub_are_not_used(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $off = new fake_output();
        $on = new fake_output();
        router::$outputs = ['off' => $off, 'on' => $on];
        set_config('order', 'off,on', 'message_notifyhub');
        set_config('off', 'off', 'message_notifyhub');

        router::deliver($this->message($user));
        $this->assertCount(0, $off->sent);
        $this->assertCount(1, $on->sent);
    }

    public function test_new_channels_join_the_order_before_email(): void {
        $this->resetAfterTest();
        router::$outputs = ['email' => new fake_output(), 'ntfy' => new fake_output(), 'telegram' => new fake_output()];
        set_config('order', 'ntfy,email', 'message_notifyhub');
        $this->assertSame(['ntfy', 'telegram', 'email'], channels::ordered());
        channels::move('email', -1);
        $this->assertSame(['ntfy', 'email', 'telegram'], channels::ordered());
    }

    public function test_the_log_never_keeps_the_message_text(): void {
        global $DB;
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        router::$outputs = ['first' => new fake_output()];
        router::deliver($this->message($user));
        $row = $DB->get_record('message_notifyhub_log', ['userid' => $user->id], '*', MUST_EXIST);
        $this->assertSame('mod_assign', $row->component);
        $this->assertSame('assign_notification', $row->name);
        $this->assertStringNotContainsString('123456', json_encode($row));
        $this->assertStringNotContainsString('graded', strtolower(json_encode($row)));
    }

    public function test_old_log_rows_are_removed_by_the_cleanup_task(): void {
        global $DB;
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        set_config('keepdays', 30, 'message_notifyhub');
        log::add($user->id, 'mod_assign', 'assign_notification', 'email', log::SENT, time() - 31 * DAYSECS);
        log::add($user->id, 'mod_assign', 'assign_notification', 'email', log::SENT);
        (new task\cleanup())->execute();
        $this->assertSame(1, $DB->count_records('message_notifyhub_log'));
    }

    public function test_a_test_message_goes_through_one_channel_and_is_logged(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $ntfy = new fake_output();
        router::$outputs = ['email' => new fake_output(), 'ntfy' => $ntfy];
        $this->assertTrue(router::send_test('ntfy'));
        $this->assertCount(1, $ntfy->sent);
        $this->assertSame('test', array_values(log::recent(1))[0]->name);
    }
}
