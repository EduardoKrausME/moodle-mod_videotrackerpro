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
 * manager.php
 *
 * @package   mod_videotrackerpro
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_videotrackerpro\report;

use context_module;
use local_video_bridge\analytics;
use local_video_bridge\progress\manager as progress_manager;

/**
 * Class manager.
 */
final class manager {
    /**
     * Method mediahash.
     *
     * @param \stdClass $activity Parameter activity.
     * @return string Return value.
     */
    public static function mediahash(\stdClass $activity): string {
        return progress_manager::media_hash((string)$activity->videosource, (string)$activity->sourceconfig);
    }

    /**
     * Method users.
     *
     * @param context_module $context Parameter context.
     * @param \stdClass $activity Parameter activity.
     * @return array Return value.
     */
    public static function users(context_module $context, \stdClass $activity): array {
        $cm = get_coursemodule_from_id(null, $context->instanceid, 0, false, MUST_EXIST);
        $groupid = 0;
        $groupmode = groups_get_activity_groupmode($cm);
        if ($groupmode) {
            $groupid = groups_get_activity_group($cm, true);
            if ($groupmode == SEPARATEGROUPS
                    && !has_capability('moodle/site:accessallgroups', $context)
                    && !$groupid) {
                return [];
            }
        }
        $users = get_enrolled_users(
            $context,
            'mod/videotrackerpro:view',
            $groupid,
            'u.id,u.firstname,u.lastname,u.email',
            'u.lastname,u.firstname'
        );
        if (!$users) {
            return [];
        }

        $hash = self::mediahash($activity);
        $progressrows = progress_manager::get_activity_progress(
            $context->id, 'mod_videotrackerpro', (int)$activity->id, $hash, array_keys($users)
        );
        $sessions = analytics::get_session_metrics(
            $context->id,
            'mod_videotrackerpro',
            (int)$activity->id,
            $hash,
            null,
            ['userids' => array_keys($users)]
        );

        $progressbyuser = [];
        foreach ($progressrows as $progress) {
            $progressbyuser[(int)$progress->userid] = $progress;
        }
        $sessionsbyuser = [];
        foreach ($sessions as $session) {
            $sessionsbyuser[(int)$session->userid][] = $session;
        }

        $rows = [];
        foreach ($users as $user) {
            $p = $progressbyuser[(int)$user->id] ?? null;
            $ss = $sessionsbyuser[(int)$user->id] ?? [];
            $watchtime = 0;
            $pauses = 0;
            $seeks = 0;
            $weightedrate = 0.0;
            $rateweight = 0;
            $lastview = 0;

            foreach ($ss as $s) {
                $watchtime += (int)$s->watchtime;
                $pauses += (int)$s->pauses;
                $seeks += (int)$s->seeks;
                $weight = max(1, (int)$s->watchtime);
                $weightedrate += (float)$s->speedavg * $weight;
                $rateweight += $weight;
                $lastview = max($lastview, (int)$s->timemodified);
            }

            $percent = (int)($p->percent ?? 0);
            $rows[] = [
                'user' => $user,
                'percent' => $percent,
                'watchtime' => $watchtime,
                'sessions' => count($ss),
                'pauses' => $pauses,
                'seeks' => $seeks,
                'speedavg' => $rateweight > 0 ? $weightedrate / $rateweight : 0,
                'currenttime' => (int)($p->currenttime ?? 0),
                'lastview' => $lastview ?: (int)($p->timemodified ?? 0),
                'complete' => (int)$activity->completionpercent > 0
                    && $percent >= (int)$activity->completionpercent,
            ];
        }
        return $rows;
    }

    /**
     * Method user.
     *
     * @param context_module $context Parameter context.
     * @param \stdClass $activity Parameter activity.
     * @param int $userid Parameter userid.
     * @return array Return value.
     */
    public static function user(context_module $context, \stdClass $activity, int $userid): array {
        $hash = self::mediahash($activity);
        return [
            'progress' => progress_manager::get_progress(
                $context->id, 'mod_videotrackerpro', (int)$activity->id, $hash, $userid
            ),
            'sessions' => analytics::get_session_metrics(
                $context->id, 'mod_videotrackerpro', (int)$activity->id, $hash, $userid
            ),
            'ranges' => analytics::get_watched_ranges(
                $context->id, 'mod_videotrackerpro', (int)$activity->id, $hash, $userid
            ),
        ];
    }
}
