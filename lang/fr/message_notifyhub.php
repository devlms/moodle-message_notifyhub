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
 * Strings for message_notifyhub (French).
 *
 * @package    message_notifyhub
 * @copyright  2026 DevLMS
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['actions'] = 'Actions';
$string['anyemail'] = 'Tout courriel de Moodle';
$string['channel'] = 'Canal';
$string['channels'] = 'Canaux, dans l\'ordre où le hub les essaie';
$string['deleteduser'] = 'Utilisateur supprimé';
$string['emailfailed'] = 'Courriels que Moodle n\'a pas pu envoyer ces 7 derniers jours : {$a}.';
$string['emailheading'] = 'Courriel';
$string['error:checksum'] = 'Le téléchargement de {$a} ne correspond pas à la somme de contrôle publiée par GitHub pour cette version : rien n\'a été installé.';
$string['error:download'] = 'Impossible de télécharger {$a}.';
$string['error:norelease'] = 'Aucune version avec un fichier d\'installation n\'a été trouvée sur GitHub pour {$a}.';
$string['error:notwritable'] = 'Le dossier des plugins n\'est pas accessible en écriture sur le serveur : les canaux ne peuvent pas être installés d\'ici. Installez-les à la main.';
$string['error:signature'] = 'La version de {$a} n\'est pas signée avec la clé de publication DevLMS : rien n\'a été installé.';
$string['error:unknownchannel'] = 'Le hub ne connaît pas le canal {$a}.';
$string['error:wrongplugin'] = 'Le fichier téléchargé ne contient pas {$a} : rien n\'a été installé.';
$string['event:delivered'] = 'Notification remise par le hub';
$string['event:undelivered'] = 'Notification non remise par le hub';
$string['failed7'] = 'Échecs (7 jours)';
$string['gateway'] = 'Passerelle SMS';
$string['hubintro'] = 'Le hub envoie chaque notification une seule fois, par le premier canal de cette liste que l\'utilisateur a configuré. Si ce canal échoue, il essaie le suivant. Les canaux qui reçoivent déjà une notification pour un utilisateur sont ignorés : personne ne la reçoit deux fois.';
$string['hubisoff'] = 'Le hub n\'est pas encore activé. Dans les <a href="{$a}">réglages des notifications</a>, activez Hub de notifications, puis activez-le pour les notifications qu\'il doit remettre.';
$string['hubpage'] = 'Hub de notifications';
$string['inhub'] = 'Dans le hub';
$string['install'] = 'Canaux à installer';
$string['installbutton'] = 'Installer {$a}';
$string['installed'] = 'Installé';
$string['installintro'] = 'Canaux DevLMS gratuits. Installer télécharge la dernière version depuis GitHub et vérifie sa somme de contrôle et sa signature avant de décompresser quoi que ce soit. Toute autre sortie de messages ou passerelle SMS de ce site fonctionne aussi dans le hub.';
$string['keepdays'] = 'Conserver le journal des envois (jours)';
$string['keepdays_desc'] = 'Les lignes plus anciennes sont supprimées chaque nuit. Le journal ne contient jamais le texte des messages.';
$string['kind'] = 'Type';
$string['kind:message'] = 'Canal de notification';
$string['kind:sms'] = 'Passerelle SMS';
$string['mailhealth'] = 'Vérifier SPF, DKIM et DMARC avec Mail health';
$string['mailhealthget'] = 'Pour savoir pourquoi les courriels arrivent dans les spams (SPF, DKIM, DMARC), installez le plugin gratuit Mail health (tool_mailhealth).';
$string['managesms'] = 'Gérer les passerelles SMS';
$string['manualinstall'] = 'Installation manuelle : <a href="{$a}">téléchargez la version</a>, décompressez-la dans le dossier du plugin, puis ouvrez Administration du site > Notifications.';
$string['movedown'] = 'Descendre {$a}';
$string['moveup'] = 'Monter {$a}';
$string['nochannels'] = 'Aucun canal n\'est activé sur ce site. Activez des sorties dans les <a href="{$a}">réglages des notifications</a>.';
$string['norecent'] = 'Rien pour l\'instant. Des lignes apparaissent quand le hub remet une notification, qu\'un test est envoyé ou qu\'un courriel échoue.';
$string['nosms'] = 'Aucun SMS envoyé ces 7 derniers jours.';
$string['notification'] = 'Notification';
$string['off'] = 'Désactivé';
$string['on'] = 'Activé';
$string['order'] = 'Ordre';
$string['pending'] = 'En attente';
$string['pluginname'] = 'Hub de notifications';
$string['privacy:metadata:log'] = 'Une ligne par tentative d\'envoi, conservée le nombre de jours fixé par l\'administrateur. Elle ne contient jamais le texte du message.';
$string['privacy:metadata:log:channel'] = 'Le canal essayé';
$string['privacy:metadata:log:component'] = 'Le composant qui a envoyé la notification';
$string['privacy:metadata:log:name'] = 'Le type de notification';
$string['privacy:metadata:log:status'] = 'Si elle a été remise';
$string['privacy:metadata:log:timecreated'] = 'Quand';
$string['privacy:metadata:log:userid'] = 'L\'utilisateur destinataire de la notification';
$string['recent'] = 'Envois récents';
$string['regions'] = 'Couverture';
$string['sendtest'] = 'M\'envoyer un test par {$a}';
$string['sent7'] = 'Envoyés (7 jours)';
$string['settingsintro'] = 'L\'ordre des canaux, les résultats et les installations sont sur la <a href="{$a}">page du hub de notifications</a>.';
$string['smsheading'] = 'SMS';
$string['status'] = 'Résultat';
$string['status:emailfailed'] = 'Courriel en échec';
$string['status:failed'] = 'Échec';
$string['status:sent'] = 'Envoyé';
$string['status:undelivered'] = 'Non remis';
$string['task:cleanup'] = 'Supprimer les anciennes lignes du journal du hub de notifications';
$string['test:body'] = 'Ceci est un test du hub de notifications de {$a}. Si vous le lisez, ce canal fonctionne pour vous.';
$string['test:failed'] = 'Le test par {$a} n\'a pas été remis. Vérifiez que vous avez configuré ce canal dans vos préférences de notification, ainsi que les réglages du canal.';
$string['test:sent'] = 'Test envoyé par {$a}.';
$string['test:subject'] = 'Test du hub de notifications';
$string['testlabel'] = 'Test du hub';
$string['testmail'] = 'Envoyer un courriel de test (page de test de Moodle)';
$string['time'] = 'Heure';
$string['turnoff'] = 'Retirer {$a} du hub';
$string['turnon'] = 'Utiliser {$a} dans le hub';
$string['user'] = 'Utilisateur';
