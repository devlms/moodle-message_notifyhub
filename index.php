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

/**
 * The notification hub page: channel order, results, SMS and email, recent deliveries, channels to install.
 *
 * @package    message_notifyhub
 * @copyright  2026 DevLMS
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use message_notifyhub\local\catalogue;
use message_notifyhub\local\channels;
use message_notifyhub\local\installer;
use message_notifyhub\local\log;
use message_notifyhub\local\router;
use message_notifyhub\local\sms;

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

admin_externalpage_setup('message_notifyhub_hub');
$url = new moodle_url('/message/output/notifyhub/index.php');

$action = optional_param('action', '', PARAM_ALPHA);
$channel = optional_param('channel', '', PARAM_PLUGIN);
$component = optional_param('component', '', PARAM_COMPONENT);
if ($action !== '') {
    require_sesskey();
    $known = in_array($channel, channels::ordered(), true);
    $label = $known ? get_string('pluginname', 'message_' . $channel) : '';
    if ($action === 'up' || $action === 'down') {
        channels::move($channel, $action === 'up' ? -1 : 1);
        redirect($url);
    }
    if (($action === 'on' || $action === 'off') && $known) {
        channels::set_on($channel, $action === 'on');
        redirect($url);
    }
    if ($action === 'test' && $known) {
        $ok = router::send_test($channel);
        redirect(
            $url,
            get_string($ok ? 'test:sent' : 'test:failed', 'message_notifyhub', $label),
            null,
            $ok ? \core\output\notification::NOTIFY_SUCCESS : \core\output\notification::NOTIFY_ERROR
        );
    }
    if ($action === 'install' && catalogue::get($component)) {
        try {
            installer::install($component);
        } catch (moodle_exception $e) {
            redirect($url, $e->getMessage(), null, \core\output\notification::NOTIFY_ERROR);
        }
        // Moodle's upgrade screen finishes the install.
        redirect(new moodle_url('/admin/index.php'));
    }
    redirect($url);
}

$strings = get_string_manager();
$channellabel = fn(string $name): string => $strings->string_exists('pluginname', 'message_' . $name)
    ? get_string('pluginname', 'message_' . $name) : $name;
// Moodle's own name for a notification type, as on the preferences page; the code names when there is none.
$notificationlabel = function (string $component, string $name) use ($strings): string {
    if ($component === 'message_notifyhub' && $name === 'test') {
        return get_string('testlabel', 'message_notifyhub');
    }
    if ($component === 'core' && $name === 'email') {
        return get_string('anyemail', 'message_notifyhub');
    }
    if ($strings->string_exists('messageprovider:' . $name, $component)) {
        return get_string('messageprovider:' . $name, $component);
    }
    return $component . ' / ' . $name;
};

$week = time() - 7 * DAYSECS;
$counts = log::counts($week);
$order = channels::ordered();
$outputs = channels::outputs();
$rows = [];
foreach ($order as $i => $name) {
    $off = channels::is_off($name);
    $link = fn($a) => (new moodle_url($url, ['action' => $a, 'channel' => $name, 'sesskey' => sesskey()]))->out(false);
    $rows[] = [
        'position' => $i + 1,
        'name' => $name,
        'label' => $channellabel($name),
        'on' => !$off,
        'sent' => $counts[$name][log::SENT] ?? 0,
        'failed' => $counts[$name][log::FAILED] ?? 0,
        'upurl' => $i > 0 ? $link('up') : '',
        'downurl' => $i < count($order) - 1 ? $link('down') : '',
        'toggleurl' => $link($off ? 'on' : 'off'),
        'testurl' => $link('test'),
    ];
}

$deliveries = [];
$userids = [];
$recent = log::recent(50);
foreach ($recent as $row) {
    $userids[$row->userid] = $row->userid;
}
$namefields = 'id, ' . implode(', ', \core_user\fields::get_name_fields());
$users = $userids ? $DB->get_records_list('user', 'id', $userids, '', $namefields) : [];
foreach ($recent as $row) {
    $user = $users[$row->userid] ?? null;
    $deliveries[] = [
        'time' => userdate($row->timecreated, get_string('strftimedatetimeshort', 'core_langconfig')),
        'user' => $user ? fullname($user) : get_string('deleteduser', 'message_notifyhub'),
        'userurl' => $user ? (new moodle_url('/user/profile.php', ['id' => $user->id]))->out(false) : '',
        'notification' => $notificationlabel($row->component, $row->name),
        'channel' => $row->channel === '' ? '-' : $channellabel($row->channel),
        'status' => get_string('status:' . $row->status, 'message_notifyhub'),
        'ok' => $row->status === log::SENT,
    ];
}

$catalogue = [];
foreach (catalogue::all() as $key => $entry) {
    // GitHub is asked only when an admin presses Install, so this page never waits on it.
    $present = installer::is_present($key);
    $canwrite = installer::can_install($key);
    $catalogue[] = [
        'component' => $key,
        'label' => $present ? get_string('pluginname', $key) : $key,
        'kind' => get_string('kind:' . $entry['kind'], 'message_notifyhub'),
        'regions' => $entry['regions'],
        'guide' => $entry['guide'],
        'repo' => 'https://github.com/' . $entry['repo'],
        'installed' => $present,
        'caninstall' => !$present && $canwrite,
        'manual' => !$present && !$canwrite,
        'releasesurl' => 'https://github.com/' . $entry['repo'] . '/releases',
    ];
}

$hubon = !empty(get_message_processors(true)['notifyhub']);
$mailhealth = core_component::get_component_directory('tool_mailhealth') !== null;
echo $OUTPUT->header();
echo $OUTPUT->render_from_template('message_notifyhub/hub', [
    'hubon' => $hubon,
    'notificationsurl' => (new moodle_url('/admin/message.php'))->out(false),
    'channels' => $rows,
    'haschannels' => !empty($rows),
    'sms' => sms::summary($week),
    'smsurl' => (new moodle_url('/sms/sms_gateways.php'))->out(false),
    'emailfailed' => $counts['email'][log::EMAILFAILED] ?? 0,
    'testmailurl' => (new moodle_url('/admin/testoutgoingmailconf.php'))->out(false),
    'mailhealthurl' => $mailhealth ? (new moodle_url('/admin/tool/mailhealth/index.php'))->out(false) : '',
    'deliveries' => $deliveries,
    'catalogue' => $catalogue,
    'actionurl' => $url->out(false),
    'sesskey' => sesskey(),
]);
echo $OUTPUT->footer();
