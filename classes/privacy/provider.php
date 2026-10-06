<?php
namespace mod_videotrackerpro\privacy;

use core_privacy\local\metadata\collection;

/**
 * Privacy provider.
 *
 * The activity itself stores no learner playback rows. Personal playback data is
 * owned, exported and deleted by local_video_bridge in the module context.
 */
class provider implements
        \core_privacy\local\metadata\provider,
        \core_privacy\local\request\core_userlist_provider,
        \core_privacy\local\request\plugin\provider {

    public static function get_metadata(collection $collection): collection {
        $collection->add_subsystem_link('local_video_bridge', [], 'privacy:metadata:bridge');
        return $collection;
    }

    public static function get_contexts_for_userid(int $userid): \core_privacy\local\request\contextlist {
        $list = new \core_privacy\local\request\contextlist();
        $sql = "SELECT DISTINCT c.id
                  FROM {context} c
                  JOIN {course_modules} cm ON cm.id = c.instanceid
                  JOIN {modules} m ON m.id = cm.module
                  JOIN {local_video_bridge_progress} p ON p.contextid = c.id
                 WHERE c.contextlevel = :contextlevel
                   AND m.name = :modname
                   AND p.component = :component
                   AND p.userid = :userid";
        $list->add_from_sql($sql, [
            'contextlevel' => CONTEXT_MODULE,
            'modname' => 'videotrackerpro',
            'component' => 'mod_videotrackerpro',
            'userid' => $userid,
        ]);
        return $list;
    }

    public static function export_user_data(\core_privacy\local\request\approved_contextlist $contextlist): void {
        // local_video_bridge owns and exports playback rows for these contexts.
    }

    public static function delete_data_for_all_users_in_context(\context $context): void {
        if (!$context instanceof \context_module) {
            return;
        }
        $cm = get_coursemodule_from_id('videotrackerpro', $context->instanceid, 0, false, IGNORE_MISSING);
        if ($cm) {
            \local_video_bridge\progress\manager::delete_consumer(
                $context->id,
                'mod_videotrackerpro',
                (int)$cm->instance
            );
        }
    }

    public static function delete_data_for_user(\core_privacy\local\request\approved_contextlist $contextlist): void {
        global $DB;
        $userid = (int)$contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            $cm = get_coursemodule_from_id('videotrackerpro', $context->instanceid, 0, false, IGNORE_MISSING);
            if (!$cm) {
                continue;
            }
            $params = [
                'contextid' => $context->id,
                'component' => 'mod_videotrackerpro',
                'itemid' => (int)$cm->instance,
                'userid' => $userid,
            ];
            $DB->delete_records('local_video_bridge_progress', $params);
            $DB->delete_records('local_video_bridge_session', $params);
        }
    }

    public static function get_users_in_context(\core_privacy\local\request\userlist $userlist): void {
        $context = $userlist->get_context();
        if (!$context instanceof \context_module) {
            return;
        }
        $sql = "SELECT p.userid
                  FROM {local_video_bridge_progress} p
                 WHERE p.contextid = :contextid
                   AND p.component = :component";
        $userlist->add_from_sql('userid', $sql, [
            'contextid' => $context->id,
            'component' => 'mod_videotrackerpro',
        ]);
    }

    public static function delete_data_for_users(\core_privacy\local\request\approved_userlist $userlist): void {
        global $DB;
        $context = $userlist->get_context();
        if (!$context instanceof \context_module) {
            return;
        }
        foreach ($userlist->get_userids() as $userid) {
            $params = [
                'contextid' => $context->id,
                'component' => 'mod_videotrackerpro',
                'userid' => (int)$userid,
            ];
            $DB->delete_records('local_video_bridge_progress', $params);
            $DB->delete_records('local_video_bridge_session', $params);
        }
    }
}
