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
 * Strings for message_notifyhub (Arabic).
 *
 * @package    message_notifyhub
 * @copyright  2026 DevLMS
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['actions'] = 'الإجراءات';
$string['channel'] = 'القناة';
$string['channels'] = 'القنوات، بالترتيب الذي يجرّبها به المركز';
$string['deleteduser'] = 'مستخدم محذوف';
$string['emailfailed'] = 'رسائل بريد لم يتمكن Moodle من إرسالها في آخر 7 أيام: {$a}.';
$string['emailheading'] = 'البريد الإلكتروني';
$string['error:checksum'] = 'الملف الذي نُزّل لـ {$a} لا يطابق المجموع الاختباري الذي ينشره GitHub لهذا الإصدار، لذلك لم يُثبَّت شيء.';
$string['error:download'] = 'تعذّر تنزيل {$a}.';
$string['error:norelease'] = 'لم يُعثر على GitHub على إصدار يحتوي ملف تثبيت لـ {$a}.';
$string['error:notwritable'] = 'مجلد الإضافات على الخادم غير قابل للكتابة، لذلك لا يمكن تثبيت القنوات من هنا. ثبّتها يدويًا.';
$string['error:signature'] = 'إصدار {$a} غير موقّع بمفتاح إصدارات DevLMS، لذلك لم يُثبَّت شيء.';
$string['error:unknownchannel'] = 'المركز لا يعرف القناة {$a}.';
$string['error:wrongplugin'] = 'الملف الذي نُزّل لا يحتوي {$a}، لذلك لم يُثبَّت شيء.';
$string['event:delivered'] = 'أوصل المركز إشعارًا';
$string['event:undelivered'] = 'لم يوصل المركز إشعارًا';
$string['failed7'] = 'فشل (7 أيام)';
$string['gateway'] = 'بوابة الرسائل القصيرة';
$string['hubintro'] = 'يرسل المركز كل إشعار مرة واحدة، عبر أول قناة في هذه القائمة أعدّها المستخدم. إذا فشلت هذه القناة يجرّب التالية. تُتخطّى القنوات التي يصلها الإشعار أصلًا لهذا المستخدم، فلا يصل أحدًا مرتين.';
$string['hubisoff'] = 'المركز غير مفعّل بعد. في <a href="{$a}">إعدادات الإشعارات</a> فعّل مركز الإشعارات، ثم فعّله للإشعارات التي يجب أن يوصلها.';
$string['hubpage'] = 'مركز الإشعارات';
$string['inhub'] = 'في المركز';
$string['install'] = 'قنوات يمكنك تثبيتها';
$string['installbutton'] = 'تثبيت {$a}';
$string['installed'] = 'مثبّتة';
$string['installintro'] = 'قنوات DevLMS مجانية. زر التثبيت ينزّل أحدث إصدار من GitHub ويتحقق من مجموعه الاختباري وتوقيعه قبل فك أي شيء. أي مخرج رسائل أو بوابة رسائل قصيرة أخرى على هذا الموقع تعمل في المركز أيضًا.';
$string['keepdays'] = 'مدة الاحتفاظ بسجل الإرسال (أيام)';
$string['keepdays_desc'] = 'تُحذف الأسطر الأقدم كل ليلة. لا يحتفظ السجل بنص الرسائل أبدًا.';
$string['kind'] = 'النوع';
$string['kind:message'] = 'قناة إشعارات';
$string['kind:sms'] = 'بوابة رسائل قصيرة';
$string['mailhealth'] = 'افحص SPF وDKIM وDMARC بإضافة Mail health';
$string['mailhealthget'] = 'لمعرفة سبب وصول البريد إلى الرسائل المزعجة (SPF وDKIM وDMARC)، ثبّت الإضافة المجانية Mail health ‏(tool_mailhealth).';
$string['managesms'] = 'إدارة بوابات الرسائل القصيرة';
$string['manualinstall'] = 'التثبيت اليدوي: <a href="{$a}">نزّل الإصدار</a>، وفكّه في مجلد الإضافة، ثم افتح إدارة الموقع > الإشعارات.';
$string['movedown'] = 'إنزال {$a}';
$string['moveup'] = 'رفع {$a}';
$string['nochannels'] = 'لا توجد قناة مفعّلة على هذا الموقع. فعّل المخرجات في <a href="{$a}">إعدادات الإشعارات</a>.';
$string['norecent'] = 'لا شيء بعد. تظهر الأسطر عندما يوصل المركز إشعارًا، أو يُرسل اختبار، أو يفشل بريد.';
$string['nosms'] = 'لم تُرسل رسائل قصيرة في آخر 7 أيام.';
$string['notification'] = 'الإشعار';
$string['off'] = 'معطّلة';
$string['on'] = 'مفعّلة';
$string['order'] = 'الترتيب';
$string['pending'] = 'قيد الانتظار';
$string['pluginname'] = 'مركز الإشعارات';
$string['privacy:metadata:log'] = 'سطر لكل محاولة إرسال، يُحتفظ به لعدد الأيام الذي يحدده المسؤول. لا يحتوي نص الرسالة أبدًا.';
$string['privacy:metadata:log:channel'] = 'القناة التي جُرّبت';
$string['privacy:metadata:log:component'] = 'المكوّن الذي أرسل الإشعار';
$string['privacy:metadata:log:name'] = 'نوع الإشعار';
$string['privacy:metadata:log:status'] = 'هل وصل';
$string['privacy:metadata:log:timecreated'] = 'متى';
$string['privacy:metadata:log:userid'] = 'المستخدم الموجّه إليه الإشعار';
$string['recent'] = 'آخر عمليات الإرسال';
$string['regions'] = 'التغطية';
$string['sendtest'] = 'أرسل لي اختبارًا عبر {$a}';
$string['sent7'] = 'أُرسلت (7 أيام)';
$string['settingsintro'] = 'ترتيب القنوات والنتائج والتثبيت في <a href="{$a}">صفحة مركز الإشعارات</a>.';
$string['smsheading'] = 'الرسائل القصيرة';
$string['status'] = 'النتيجة';
$string['status:emailfailed'] = 'فشل البريد';
$string['status:failed'] = 'فشل';
$string['status:sent'] = 'أُرسل';
$string['status:undelivered'] = 'لم يصل';
$string['task:cleanup'] = 'حذف الأسطر القديمة من سجل مركز الإشعارات';
$string['test:body'] = 'هذا اختبار من مركز الإشعارات في {$a}. إذا كنت تقرأه فهذه القناة تعمل لديك.';
$string['test:failed'] = 'لم يصل الاختبار عبر {$a}. تحقّق من أنك أعددت هذه القناة في تفضيلات الإشعارات، ومن إعدادات القناة نفسها.';
$string['test:sent'] = 'أُرسل الاختبار عبر {$a}.';
$string['test:subject'] = 'اختبار مركز الإشعارات';
$string['testmail'] = 'أرسل بريدًا تجريبيًا (صفحة الاختبار في Moodle)';
$string['time'] = 'الوقت';
$string['turnoff'] = 'إخراج {$a} من المركز';
$string['turnon'] = 'استخدام {$a} في المركز';
$string['user'] = 'المستخدم';
