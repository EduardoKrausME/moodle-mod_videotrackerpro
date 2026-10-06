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
 * lib.php
 *
 * @package   mod_videotrackerpro
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_videotrackerpro\source_manager;

defined('MOODLE_INTERNAL') || die;

function videotrackerpro_supports($feature) {
    return match ($feature) {
        FEATURE_MOD_ARCHETYPE => MOD_ARCHETYPE_OTHER,
        FEATURE_GROUPS, FEATURE_GROUPINGS, FEATURE_MOD_INTRO, FEATURE_SHOW_DESCRIPTION,
        FEATURE_COMPLETION_TRACKS_VIEWS, FEATURE_COMPLETION_HAS_RULES, FEATURE_BACKUP_MOODLE2 => true,
        FEATURE_MOD_PURPOSE => MOD_PURPOSE_CONTENT,
        default => null,
    };
}

function videotrackerpro_add_instance(stdClass $data, ?mod_videotrackerpro_mod_form $mform = null): int {
    global $DB;
    $data->timecreated = $data->timemodified = time();
    source_manager::require_tracking((string)$data->videosource);
    source_manager::create()->normalise_record($data);
    $id = $DB->insert_record('videotrackerpro', $data);
    $data->id = $id;
    source_manager::create()->save_files($data, context_module::instance((int)$data->coursemodule));
    return $id;
}

function videotrackerpro_update_instance(stdClass $data, ?mod_videotrackerpro_mod_form $mform = null): bool {
    global $DB;
    $old = $DB->get_record('videotrackerpro', ['id' => $data->instance], '*', MUST_EXIST);
    $data->id = $data->instance;
    $data->timemodified = time();
    source_manager::require_tracking((string)$data->videosource);
    source_manager::create()->normalise_record($data);
    $ok = $DB->update_record('videotrackerpro', $data);
    source_manager::create()->save_files($data, context_module::instance((int)$data->coursemodule), (string)$old->videosource);
    return $ok;
}

function videotrackerpro_delete_instance(int $id): bool {
    global $DB;
    $activity = $DB->get_record('videotrackerpro', ['id' => $id]);
    if (!$activity) {
        return false;
    }
    $cm = get_coursemodule_from_instance('videotrackerpro', $id, $activity->course, false, IGNORE_MISSING);
    if ($cm) {
        $context = context_module::instance($cm->id);
        source_manager::create()->delete_files($context);
        \local_video_bridge\progress\manager::delete_consumer($context->id, 'mod_videotrackerpro', $id);
    }
    $DB->delete_records('videotrackerpro', ['id' => $id]);
    return true;
}

function videotrackerpro_get_completion_state($course, $cm, int $userid, bool $type): bool {
    global $DB;
    $activity = $DB->get_record('videotrackerpro', ['id' => $cm->instance], '*', MUST_EXIST);
    if ((int)$activity->completionpercent <= 0) {
        return $type;
    }
    $context = context_module::instance($cm->id);
    $hash = \local_video_bridge\progress\manager::media_hash((string)$activity->videosource, (string)$activity->sourceconfig);
    $progress = \local_video_bridge\progress\manager::get_progress($context->id, 'mod_videotrackerpro', (int)$activity->id, $hash, $userid);
    return $progress && (int)$progress->percent >= (int)$activity->completionpercent;
}
