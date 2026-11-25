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

use message_notifyhub\local\installer;
use message_notifyhub\local\catalogue;
use message_notifyhub\local\log;
use message_notifyhub\local\releases;
use message_notifyhub\local\signature;
use message_notifyhub\local\sms;

/**
 * The hub inside Moodle: real message_send, failed emails, SMS results, installing channels.
 *
 * @package    message_notifyhub
 * @copyright  2026 DevLMS
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \message_output_notifyhub
 * @covers     \message_notifyhub\observer
 * @covers     \message_notifyhub\local\sms
 * @covers     \message_notifyhub\local\installer
 * @covers     \message_notifyhub\local\releases
 * @covers     \message_notifyhub\local\signature
 */
final class hub_test extends \advanced_testcase {
    #[\Override]
    protected function tearDown(): void {
        catalogue::$override = null;
        releases::$http = null;
        signature::$publickey = null;
        parent::tearDown();
    }

    /**
     * Send an assignment notification through core.
     *
     * @param \stdClass $user
     */
    private function notify(\stdClass $user): void {
        $message = new \core\message\message();
        $message->component = 'mod_assign';
        $message->name = 'assign_notification';
        $message->userfrom = \core_user::get_noreply_user();
        $message->userto = $user;
        $message->subject = 'Graded';
        $message->fullmessage = 'Your essay was graded.';
        $message->fullmessageformat = FORMAT_PLAIN;
        $message->fullmessagehtml = '';
        $message->smallmessage = 'Graded';
        $message->notification = 1;
        message_send($message);
    }

    public function test_message_send_reaches_the_user_once_through_the_hub(): void {
        $this->resetAfterTest();
        $this->preventResetByRollback();
        $user = $this->getDataGenerator()->create_user();
        \core\plugininfo\message::enable_plugin('notifyhub', 1);
        set_config('order', 'email', 'message_notifyhub');
        $sink = $this->redirectEmails();

        // Only the hub is on: it chooses email.
        set_config('message_provider_mod_assign_assign_notification_enabled', 'notifyhub', 'message');
        $this->notify($user);
        $this->assertSame(1, $sink->count());
        $this->assertSame(log::SENT, array_values(log::recent(1))[0]->status);

        // Email and the hub are both on: still one email.
        $sink->clear();
        set_config('message_provider_mod_assign_assign_notification_enabled', 'email,notifyhub', 'message');
        $this->notify($user);
        $this->assertSame(1, $sink->count());
    }

    public function test_failed_emails_are_logged_without_their_text(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        \core\event\email_failed::create([
            'context' => \context_system::instance(),
            'userid' => get_admin()->id,
            'relateduserid' => $user->id,
            'other' => ['subject' => 'Your code', 'message' => 'Code 654321', 'errorinfo' => 'SMTP connect() failed'],
        ])->trigger();
        $row = array_values(log::recent(1))[0];
        $this->assertSame(log::EMAILFAILED, $row->status);
        $this->assertSame('email', $row->channel);
        $this->assertEquals($user->id, $row->userid);
        $this->assertStringNotContainsString('654321', json_encode($row));
    }

    public function test_sms_results_are_counted_per_gateway(): void {
        global $DB;
        $this->resetAfterTest();
        $gw = $DB->insert_record('sms_gateways', (object) ['name' => 'Kenya', 'gateway' => 'smsgateway_x\gateway',
            'enabled' => 1, 'config' => '{}']);
        foreach (['gateway_sent', 'gateway_sent', 'gateway_failed', 'gateway_queued'] as $status) {
            $DB->insert_record('sms_messages', (object) ['recipientnumber' => '+254700000000', 'content' => 'x',
                'component' => 'core', 'messagetype' => 'test', 'issensitive' => 0, 'gatewayid' => $gw,
                'status' => $status, 'timecreated' => time()]);
        }
        $summary = sms::summary(time() - DAYSECS);
        $this->assertSame([['name' => 'Kenya', 'sent' => 2, 'failed' => 1, 'pending' => 1]], $summary);
    }

    /**
     * Make the catalogue know one test channel, and GitHub answer with one release of it.
     *
     * @param string $zipbytes
     * @param string|null $digest
     * @return string The release's signature, made with a key the hub is told to trust.
     */
    private function github_release(string $zipbytes, ?string $digest = null): string {
        $pair = sodium_crypto_sign_keypair();
        signature::$publickey = base64_encode(sodium_crypto_sign_publickey($pair));
        $sig = base64_encode(sodium_crypto_sign_detached($zipbytes, sodium_crypto_sign_secretkey($pair)));
        catalogue::$override = ['message_hubtestout' => ['kind' => 'message', 'regions' => 'Worldwide',
            'repo' => 'devlms/moodle-message_hubtestout', 'guide' => '']];
        releases::$http = fn($url) => json_encode([
            ['draft' => true, 'tag_name' => 'v9.0', 'assets' => []],
            ['draft' => false, 'prerelease' => true, 'tag_name' => 'v1.0', 'assets' => [[
                'name' => 'message_hubtestout-1.0.zip',
                'browser_download_url' => 'https://github.com/devlms/moodle-message_hubtestout/releases/download/v1.0/x.zip',
                'digest' => 'sha256:' . ($digest ?? hash('sha256', $zipbytes)),
            ], [
                'name' => 'message_hubtestout-1.0.zip.sig',
                'browser_download_url' => 'https://github.com/devlms/moodle-message_hubtestout/releases/download/v1.0/x.zip.sig',
            ]]],
        ]);
        return $sig;
    }

    /**
     * A plugin zip whose root folder has another name: the installer renames it.
     *
     * @param string $component
     * @return string
     */
    private function plugin_zip(string $component): string {
        $file = make_request_directory() . '/z.zip';
        $zip = new \ZipArchive();
        $zip->open($file, \ZipArchive::CREATE);
        $zip->addFromString(
            'moodle-message_hubtestout-1.0/version.php',
            "<?php\ndefined('MOODLE_INTERNAL') || die();\n\$plugin->component = '$component';\n\$plugin->version = 1;\n"
        );
        $zip->close();
        return file_get_contents($file);
    }

    public function test_the_newest_release_comes_from_github_and_is_kept_for_an_hour(): void {
        $this->resetAfterTest();
        $bytes = $this->plugin_zip('message_hubtestout');
        $this->github_release($bytes);
        $release = releases::latest('message_hubtestout', 'devlms/moodle-message_hubtestout', true);
        $this->assertSame('1.0', $release['release'], 'Drafts are skipped; a prerelease counts.');
        $this->assertSame(hash('sha256', $bytes), $release['sha256']);
        releases::$http = fn($url) => throw new \coding_exception('asked again');
        $this->assertSame($release, releases::latest('message_hubtestout', 'devlms/moodle-message_hubtestout'));
    }

    public function test_installer_refuses_bad_checksums_signatures_and_other_plugins(): void {
        global $CFG;
        $this->resetAfterTest();
        $this->setAdminUser();
        if (!installer::can_install('message_hubtestout')) {
            $this->markTestSkipped('The message output folder is not writable here.');
        }
        $good = $this->plugin_zip('message_hubtestout');
        $sig = $this->github_release($good);
        $cases = [
            'error:checksum' => fn($url) => str_ends_with($url, '.sig') ? $sig : 'a tampered zip',
            'error:signature' => fn($url) => str_ends_with($url, '.sig') ? base64_encode(str_repeat('x', 64)) : $good,
        ];
        foreach ($cases as $code => $fetch) {
            $this->github_release($good);
            try {
                installer::install('message_hubtestout', $fetch);
                $this->fail("Installed despite $code");
            } catch (\moodle_exception $e) {
                $this->assertSame($code, $e->errorcode);
            }
        }
        $other = $this->plugin_zip('local_somethingelse');
        $othersig = $this->github_release($other);
        try {
            installer::install('message_hubtestout', fn($url) => str_ends_with($url, '.sig') ? $othersig : $other);
            $this->fail('Installed a zip of another plugin');
        } catch (\moodle_exception $e) {
            $this->assertSame('error:wrongplugin', $e->errorcode);
        }
        $this->assertDirectoryDoesNotExist($CFG->dirroot . '/message/output/hubtestout');
    }

    public function test_installer_unpacks_a_signed_release_into_the_right_folder(): void {
        global $CFG;
        $this->resetAfterTest();
        $this->setAdminUser();
        if (!installer::can_install('message_hubtestout')) {
            $this->markTestSkipped('The message output folder is not writable here.');
        }
        $bytes = $this->plugin_zip('message_hubtestout');
        $sig = $this->github_release($bytes);
        $target = $CFG->dirroot . '/message/output/hubtestout';
        try {
            installer::install('message_hubtestout', fn($url) => str_ends_with($url, '.sig') ? $sig : $bytes);
            $this->assertFileExists($target . '/version.php');
        } finally {
            remove_dir($target);
            \core_component::reset();
        }
    }

    public function test_the_catalogue_lists_only_devlms_repositories(): void {
        foreach (catalogue::all() as $component => $entry) {
            $this->assertMatchesRegularExpression('/^(message|smsgateway)_[a-z0-9]+$/', $component);
            $this->assertStringStartsWith('devlms/moodle-' . $component, $entry['repo']);
        }
    }
}
