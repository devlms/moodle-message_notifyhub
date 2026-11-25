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
 * Installs a channel plugin from its signed GitHub release, with the same checks as the DevLMS payment hub.
 *
 * Nothing is unpacked unless the zip is exactly the file GitHub published for the newest release (SHA-256), it is
 * signed with the DevLMS release key (Ed25519), and its version.php declares the expected component. Moodle's
 * upgrade screen then finishes the install.
 *
 * @package    message_notifyhub
 * @copyright  2026 DevLMS
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class installer {
    /**
     * Whether this site can install a component from the web: its plugin type folder must be writable.
     *
     * @param string $component
     * @return bool
     */
    public static function can_install(string $component): bool {
        [$type] = \core_component::normalize_component($component);
        return \core_plugin_manager::instance()->is_plugintype_writable($type);
    }

    /**
     * Whether a component is installed (its code is on the site).
     *
     * @param string $component
     * @return bool
     */
    public static function is_present(string $component): bool {
        [$type, $name] = \core_component::normalize_component($component);
        return array_key_exists($name, \core_component::get_plugin_list($type));
    }

    /**
     * Download, check and unpack one channel.
     *
     * @param string $component Catalogue key.
     * @param callable|null $fetch For tests: url => file content.
     */
    public static function install(string $component, ?callable $fetch = null): void {
        global $CFG;
        require_once($CFG->libdir . '/filelib.php');
        require_capability('moodle/site:config', \context_system::instance());
        $entry = catalogue::get($component);
        if (!$entry) {
            throw new \moodle_exception('error:unknownchannel', 'message_notifyhub', '', $component);
        }
        if (self::is_present($component)) {
            return;
        }
        if (!self::can_install($component)) {
            throw new \moodle_exception('error:notwritable', 'message_notifyhub');
        }
        $release = releases::latest($component, $entry['repo'], true);
        if (!$release) {
            throw new \moodle_exception('error:norelease', 'message_notifyhub', '', $entry['repo']);
        }
        $zip = $fetch ? $fetch($release['url']) : download_file_content($release['url'], null, null, false, 120, 20);
        if (!is_string($zip) || $zip === '') {
            throw new \moodle_exception('error:download', 'message_notifyhub', '', $release['url']);
        }
        if (!hash_equals($release['sha256'], hash('sha256', $zip))) {
            throw new \moodle_exception('error:checksum', 'message_notifyhub', '', $component);
        }
        $sig = $fetch ? $fetch($release['sigurl']) : download_file_content($release['sigurl'], null, null, false, 60, 20);
        if (!is_string($sig) || !signature::valid($zip, $sig)) {
            throw new \moodle_exception('error:signature', 'message_notifyhub', '', $component);
        }
        [$type, $name] = \core_component::normalize_component($component);
        $path = make_request_directory() . '/' . $name . '.zip';
        file_put_contents($path, $zip);
        $manager = new \core\update\code_manager();
        $root = $manager->get_plugin_zip_root_dir($path);
        if ($root === false || !self::is_component($path, $root, $component)) {
            throw new \moodle_exception('error:wrongplugin', 'message_notifyhub', '', $component);
        }
        $target = \core_plugin_manager::instance()->get_plugintype_root($type);
        if (!$manager->unzip_plugin_file($path, $target, $name)) {
            throw new \moodle_exception('error:download', 'message_notifyhub', '', $release['url']);
        }
        \core_component::reset();
        \core_plugin_manager::reset_caches();
    }

    /**
     * Whether the zip's version.php declares the expected component.
     *
     * @param string $path
     * @param string $root Root folder inside the zip.
     * @param string $component
     * @return bool
     */
    private static function is_component(string $path, string $root, string $component): bool {
        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) {
            return false;
        }
        $version = (string) $zip->getFromName($root . '/version.php');
        $zip->close();
        return (bool) preg_match('/\\$plugin->component\s*=\s*[\'"]' . preg_quote($component, '/') . '[\'"]\s*;/', $version);
    }
}
