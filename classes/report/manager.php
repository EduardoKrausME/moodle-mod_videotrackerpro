<?php
namespace mod_videotrackerpro\report;

use context_module;
use local_video_bridge\analytics;
use local_video_bridge\progress\manager as progress_manager;

final class manager {
    public static function mediahash(\stdClass $activity): string {
        return progress_manager::media_hash((string)$activity->videosource, (string)$activity->sourceconfig);
    }

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
            $context->id, 'mod_videotrackerpro', (int)$activity->id, $hash
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
