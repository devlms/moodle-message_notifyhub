# Notification hub for Moodle (message_notifyhub)

One place to control how Moodle reaches people: app notifications, chat apps, SMS and email.

Moodle sends a notification to every output a user has switched on, and does not notice when one fails. The hub is an output that sends each notification **once**, through the first channel the user has actually set up, and tries the next channel when one fails. Students who use the ntfy app get it there; students who set up nothing still get the email.

## What it does

- **One copy, best channel first.** You order the channels (for example ntfy, then Telegram, then email). For each user the hub skips channels they have not set up, and channels that already get that notification for them, so nobody gets it twice.
- **Falls back.** A channel counts as delivered only when it confirms it. If it fails, the next one is tried.
- **Shows what happened.** Sent and failed counts per channel for the last 7 days, the latest deliveries, SMS results per gateway (from Moodle's own SMS log), and emails Moodle could not send.
- **Test any channel.** "Send me a test" delivers through one channel to you, and the result goes in the log.
- **Installs channels.** Free DevLMS channels (ntfy, Africa's Talking SMS) install from the hub page. The hub downloads the newest GitHub release and unpacks it only when it matches the SHA-256 GitHub publishes and is signed with the DevLMS release key. Any other message output or SMS gateway on your site works in the hub too.
- **Events** "Notification delivered by the hub" and "Notification not delivered by the hub", for reports and automations.

## Set it up

1. Site administration > Messaging > Notification settings: turn on **Notification hub**. Then, for the notifications it should deliver, turn the hub on and the other channels off (otherwise users also get them there).
2. Site administration > Messaging > **Notification hub**: put the channels in order, take out any you do not want the hub to use, and send yourself a test.
3. Users set up their channels as usual under Preferences > Notification preferences (for example their ntfy topic).

## Good to know

- The log never stores message text, only who, which notification, which channel and the result. SMS can carry sign-in codes. Rows are removed after 30 days (change it in the hub's settings). Users' rows are included in Moodle's privacy export and deletion.
- SMS goes through Moodle's own SMS gateways, which already choose a gateway by country code, so the hub does not add a second SMS router.
- Why email lands in spam (SPF, DKIM, DMARC) is a separate job: the free [Mail health](https://moodle.org/plugins/tool_mailhealth) plugin checks it, and the hub links to it when it is installed.
- Popup notifications (the bell) are always kept by Moodle, so the hub does not route to them.

Moodle 4.5 to 5.3. English, French and Arabic. Built by DevLMS: https://devlms.com
