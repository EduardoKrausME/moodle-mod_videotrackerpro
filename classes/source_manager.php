<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * source_manager.php
 *
 * @package   mod_videotrackerpro
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_videotrackerpro;

/**
 * Class source_manager.
 */
final class source_manager {
    /**
     * Method create.
     *
     * @return \local_video_bridge\source\manager Return value.
     */
    public static function create(): \local_video_bridge\source\manager {
        return new \local_video_bridge\source\manager('videosource', 'sourceconfig', 'videourl');
    }

    /**
     * Validates selected source fields and enforces tracking capability.
     *
     * @param array $data Form data.
     * @param array $files Submitted files.
     * @return array Validation errors.
     */
    public static function validation(array $data, array $files): array {
        $manager = self::create();
        $errors = $manager->validation($data, $files);
        $source = clean_param((string)($data['videosource'] ?? ''), PARAM_PLUGIN);
        if ($source !== '') {
            try {
                if (!$manager->get_plugin($source)->supports('tracking')) {
                    $errors['videosource'] = get_string('errortrackingrequired', 'videotrackerpro');
                }
            } catch (\moodle_exception $exception) {
                $errors['videosource'] = $exception->getMessage();
            }
        }
        return $errors;
    }

    /**
     * Rejects a source which does not provide reliable tracking.
     *
     * @param string $source Source short name.
     * @return void
     */
    public static function require_tracking(string $source): void {
        $source = clean_param($source, PARAM_PLUGIN);
        $manager = self::create();
        if (!$manager->get_plugin($source)->supports('tracking')) {
            throw new \moodle_exception('errortrackingrequired', 'videotrackerpro');
        }
    }
}
