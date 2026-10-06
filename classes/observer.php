<?php
namespace mod_videotrackerpro;

final class observer {
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
