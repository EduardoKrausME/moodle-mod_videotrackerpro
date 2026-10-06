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
 * observer.php
 *
 * @package   mod_videotrackerpro
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_videotrackerpro;

/**
 * Class observer.
 */
final class observer {
    /**
     * Method analytics_updated.
     *
     * @param \local_video_bridge\event\analytics_updated $event Parameter event.
     * @return void Return value.
     */
    public static function analytics_updated(\local_video_bridge\event\analytics_updated $event): void {
        if (($event->other['component'] ?? '') !== 'mod_videotrackerpro') {
            return;
        }

        $context = $event->get_context();
        if (!$context instanceof \context_module) {
            return;
        }

        $cm = get_coursemodule_from_id('videotrackerpro', $context->instanceid, 0, false, IGNORE_MISSING);
        if (!$cm || (int)$cm->instance !== (int)($event->other['itemid'] ?? 0)) {
            return;
        }

        global $DB;
        $activity = $DB->get_record('videotrackerpro', ['id' => $cm->instance], 'id,course,completionpercent');
        if (!$activity || (int)$activity->completionpercent <= 0) {
            return;
        }

        $course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
        $completion = new \completion_info($course);
        if ($completion->is_enabled($cm)) {
            $completion->update_state($cm, COMPLETION_UNKNOWN, (int)$event->relateduserid);
        }
    }
}
