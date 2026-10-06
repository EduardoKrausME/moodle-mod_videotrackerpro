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
 * custom_completion.php
 *
 * @package   mod_videotrackerpro
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_videotrackerpro\completion;

use core_completion\activity_custom_completion;

/**
 * Class custom_completion.
 */
class custom_completion extends activity_custom_completion {
    /**
     * Method get_state.
     *
     * @param string $rule Parameter rule.
     * @return int Return value.
     */
    public function get_state(string $rule): int {
        if ($rule !== 'completionpercent') {
            throw new \coding_exception('Unknown completion rule: ' . $rule);
        }
        global $DB;
        $activity = $DB->get_record('videotrackerpro', ['id' => $this->cm->instance], '*', MUST_EXIST);
        if ((int)$activity->completionpercent <= 0) {
            return COMPLETION_COMPLETE;
        }
        $context = \context_module::instance($this->cm->id);
        $hash = \local_video_bridge\progress\manager::media_hash(
            (string)$activity->videosource,
            (string)$activity->sourceconfig
        );
        $progress = \local_video_bridge\progress\manager::get_progress(
            $context->id,
            'mod_videotrackerpro',
            (int)$activity->id,
            $hash,
            $this->userid
        );
        return ($progress && (int)$progress->percent >= (int)$activity->completionpercent)
            ? COMPLETION_COMPLETE : COMPLETION_INCOMPLETE;
    }

    /**
     * Method get_defined_custom_rules.
     *
     * @return array Return value.
     */
    public static function get_defined_custom_rules(): array {
        return ['completionpercent'];
    }

    /**
     * Method get_custom_rule_descriptions.
     *
     * @return array Return value.
     */
    public function get_custom_rule_descriptions(): array {
        global $DB;
        $activity = $DB->get_record('videotrackerpro', ['id' => $this->cm->instance], 'completionpercent', MUST_EXIST);
        return ['completionpercent' => get_string('completionpercent', 'videotrackerpro') .
            ': ' . (int)$activity->completionpercent . '%'];
    }

    /**
     * Method get_sort_order.
     *
     * @return array Return value.
     */
    public function get_sort_order(): array {
        return ['completionpercent'];
    }
}
