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
 * provider.php
 *
 * @package   mod_videotrackerpro
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

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

    /**
     * Method get_metadata.
     *
     * @param collection $collection Parameter collection.
     * @return collection Return value.
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_subsystem_link('local_video_bridge', [], 'privacy:metadata:bridge');
        return $collection;
    }

    /**
     * Method get_contexts_for_userid.
     *
     * @param int $userid Parameter userid.
     * @return \core_privacy\local\request\contextlist Return value.
     */
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

    /**
     * Method export_user_data.
     *
     * @param \core_privacy\local\request\approved_contextlist $contextlist Parameter contextlist.
     * @return void Return value.
     */
    public static function export_user_data(\core_privacy\local\request\approved_contextlist $contextlist): void {
        // local_video_bridge owns and exports playback rows for these contexts.
    }

    /**
     * Method delete_data_for_all_users_in_context.
     *
     * @param \context $context Parameter context.
     * @return void Return value.
     */
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

    /**
     * Method delete_data_for_user.
     *
     * @param \core_privacy\local\request\approved_contextlist $contextlist Parameter contextlist.
     * @return void Return value.
     */
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

    /**
     * Method get_users_in_context.
     *
     * @param \core_privacy\local\request\userlist $userlist Parameter userlist.
     * @return void Return value.
     */
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

    /**
     * Method delete_data_for_users.
     *
     * @param \core_privacy\local\request\approved_userlist $userlist Parameter userlist.
     * @return void Return value.
     */
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
