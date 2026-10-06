<?php
namespace mod_videotrackerpro\completion;

use core_completion\activity_custom_completion;

class custom_completion extends activity_custom_completion {
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

    public static function get_defined_custom_rules(): array {
        return ['completionpercent'];
    }

    public function get_custom_rule_descriptions(): array {
        global $DB;
        $activity = $DB->get_record('videotrackerpro', ['id' => $this->cm->instance], 'completionpercent', MUST_EXIST);
        return ['completionpercent' => get_string('completionpercent', 'videotrackerpro') . ': ' . (int)$activity->completionpercent . '%'];
    }

    public function get_sort_order(): array {
        return ['completionpercent'];
    }
}
