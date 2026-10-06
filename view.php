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
 * view.php
 *
 * @package   mod_videotrackerpro
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_video_bridge\analytics;
use mod_videotrackerpro\report\manager as report_manager;
use mod_videotrackerpro\source_manager;

require('../../config.php');

$id = required_param('id', PARAM_INT);
$cm = get_coursemodule_from_id('videotrackerpro', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
$activity = $DB->get_record('videotrackerpro', ['id' => $cm->instance], '*', MUST_EXIST);
$context = context_module::instance($cm->id);

require_login($course, true, $cm);
require_capability('mod/videotrackerpro:view', $context);

$PAGE->set_url('/mod/videotrackerpro/view.php', ['id' => $cm->id]);
$PAGE->set_context($context);
$PAGE->set_title(format_string($activity->name));
$PAGE->set_heading(format_string($course->fullname));

$completion = new completion_info($course);
if ($completion->is_enabled($cm)) {
    $completion->set_module_viewed($cm);
}
\mod_videotrackerpro\event\course_module_viewed::create([
    'objectid' => (int)$activity->id,
    'context' => $context,
])->trigger();

$bridge = source_manager::create();
$level = !empty($activity->recordsessions) ? analytics::LEVEL_DETAILED : analytics::LEVEL_BASIC;
$player = $bridge->get_player_config($activity, $context, $level);
if (!empty($player['progress'])) {
    $player['progress']['telemetryenabled'] = !empty($activity->recordsessions);
    $player['progress']['telemetryoptions'] = [
        'recordseeks' => !empty($activity->recordseeks),
        'recordpauses' => !empty($activity->recordpauses),
        'recordrates' => !empty($activity->recordrates),
        'recordbuffering' => !empty($activity->recordbuffering),
        'recorddropoff' => !empty($activity->recorddropoff),
    ];
}
$rootid = 'videotrackerpro-player-' . $cm->id;
$sourcehtml = $OUTPUT->render_from_template($player['sourcetemplate'], $player);
$PAGE->requires->js_call_amd('mod_videotrackerpro/player', 'init', [$rootid, $player]);

echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($activity->name));

if (trim((string)$activity->intro) !== '') {
    echo format_module_intro('videotrackerpro', $activity, $cm->id);
}

echo html_writer::start_div('videotrackerpro-player card mb-4', ['id' => $rootid]);
echo html_writer::div($sourcehtml, 'card-body');
echo html_writer::end_div();

if (!empty($activity->showpersonal) && !isguestuser()) {
    $data = report_manager::user($context, $activity, (int)$USER->id);
    $progress = $data['progress'];
    $watchtime = array_sum(array_map(static fn($s) => (int)$s->watchtime, $data['sessions']));
    echo $OUTPUT->render_from_template('mod_videotrackerpro/personal', [
        'percent' => (int)($progress->percent ?? 0),
        'watchtime' => format_time($watchtime),
        'sessions' => count($data['sessions']),
        'currenttime' => format_time((int)($progress->currenttime ?? 0)),
    ]);
}

if (has_capability('mod/videotrackerpro:viewreport', $context)) {
    echo html_writer::link(
        new moodle_url('/mod/videotrackerpro/report.php', ['id' => $cm->id]),
        get_string('viewreport', 'videotrackerpro'),
        ['class' => 'btn btn-secondary']
    );
}

echo $OUTPUT->footer();
