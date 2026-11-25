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
 * Strings for message_notifyhub.
 *
 * @package    message_notifyhub
 * @copyright  2026 DevLMS
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['actions'] = 'Actions';
$string['channel'] = 'Channel';
$string['channels'] = 'Channels, in the order the hub tries them';
$string['deleteduser'] = 'Deleted user';
$string['emailfailed'] = 'Emails Moodle could not send in the last 7 days: {$a}.';
$string['emailheading'] = 'Email';
$string['error:checksum'] = 'The download of {$a} does not match the checksum GitHub publishes for that release, so nothing was installed.';
$string['error:download'] = 'Could not download {$a}.';
$string['error:norelease'] = 'No release with an install file was found on GitHub for {$a}.';
$string['error:notwritable'] = 'The plugin folder on the server is not writable, so channels cannot be installed from here. Install them by hand.';
$string['error:signature'] = 'The release of {$a} is not signed with the DevLMS release key, so nothing was installed.';
$string['error:unknownchannel'] = 'The hub does not know the channel {$a}.';
$string['error:wrongplugin'] = 'The downloaded file does not contain {$a}, so nothing was installed.';
$string['event:delivered'] = 'Notification delivered by the hub';
$string['event:undelivered'] = 'Notification not delivered by the hub';
$string['failed7'] = 'Failed (7 days)';
$string['gateway'] = 'SMS gateway';
$string['hubintro'] = 'The hub sends each notification once, through the first channel in this list that the user has set up. If that channel fails, it tries the next one. Channels that already get a notification for a user are skipped, so nobody gets it twice.';
$string['hubisoff'] = 'The hub is not on yet. In <a href="{$a}">Notification settings</a>, turn on Notification hub, then turn it on for the notifications it should deliver.';
$string['hubpage'] = 'Notification hub';
$string['inhub'] = 'In the hub';
$string['install'] = 'Channels you can install';
$string['installbutton'] = 'Install {$a}';
$string['installed'] = 'Installed';
$string['installintro'] = 'Free DevLMS channels. Install downloads the newest release from GitHub and checks its checksum and signature before anything is unpacked. Any other message output or SMS gateway on this site works in the hub too.';
$string['keepdays'] = 'Keep the delivery log for (days)';
$string['keepdays_desc'] = 'Older rows are removed every night. The log never holds message text.';
$string['kind'] = 'Type';
$string['kind:message'] = 'Notification channel';
$string['kind:sms'] = 'SMS gateway';
$string['mailhealth'] = 'Check SPF, DKIM and DMARC with Mail health';
$string['mailhealthget'] = 'To check why email lands in spam (SPF, DKIM, DMARC), install the free Mail health plugin (tool_mailhealth).';
$string['managesms'] = 'Manage SMS gateways';
$string['manualinstall'] = 'Install by hand: <a href="{$a}">download the release</a>, unzip it into the plugin folder, then visit Site administration > Notifications.';
$string['movedown'] = 'Move {$a} down';
$string['moveup'] = 'Move {$a} up';
$string['nochannels'] = 'No channel is on for this site. Turn outputs on in <a href="{$a}">Notification settings</a>.';
$string['norecent'] = 'Nothing yet. Rows appear when the hub delivers a notification, a test is sent, or an email fails.';
$string['nosms'] = 'No SMS sent in the last 7 days.';
$string['notification'] = 'Notification';
$string['off'] = 'Off';
$string['on'] = 'On';
$string['order'] = 'Order';
$string['pending'] = 'Waiting';
$string['pluginname'] = 'Notification hub';
$string['privacy:metadata:log'] = 'One row per delivery attempt, kept for the number of days set by the admin. It never holds the message text.';
$string['privacy:metadata:log:channel'] = 'The channel tried';
$string['privacy:metadata:log:component'] = 'The component that sent the notification';
$string['privacy:metadata:log:name'] = 'The type of notification';
$string['privacy:metadata:log:status'] = 'Whether it was delivered';
$string['privacy:metadata:log:timecreated'] = 'When';
$string['privacy:metadata:log:userid'] = 'The user the notification was for';
$string['recent'] = 'Recent deliveries';
$string['regions'] = 'Reaches';
$string['sendtest'] = 'Send me a test through {$a}';
$string['sent7'] = 'Sent (7 days)';
$string['settingsintro'] = 'Channel order, results and installs are on the <a href="{$a}">Notification hub page</a>.';
$string['smsheading'] = 'SMS';
$string['status'] = 'Result';
$string['status:emailfailed'] = 'Email failed';
$string['status:failed'] = 'Failed';
$string['status:sent'] = 'Sent';
$string['status:undelivered'] = 'Not delivered';
$string['task:cleanup'] = 'Remove old notification hub log rows';
$string['test:body'] = 'This is a test from the notification hub on {$a}. If you can read it, this channel works for you.';
$string['test:failed'] = 'The test through {$a} was not delivered. Check that you set up this channel in your notification preferences, and the channel\'s own settings.';
$string['test:sent'] = 'Test sent through {$a}.';
$string['test:subject'] = 'Notification hub test';
$string['testmail'] = 'Send a test email (Moodle\'s own test page)';
$string['time'] = 'Time';
$string['turnoff'] = 'Take {$a} out of the hub';
$string['turnon'] = 'Use {$a} in the hub';
$string['user'] = 'User';
