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
 * report.php
 *
 * @package   mod_videotrackerpro
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_videotrackerpro\report\manager;

require('../../config.php');

$id = required_param('id', PARAM_INT);
$userid = optional_param('userid', 0, PARAM_INT);
$cm = get_coursemodule_from_id('videotrackerpro', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
$activity = $DB->get_record('videotrackerpro', ['id' => $cm->instance], '*', MUST_EXIST);
$context = context_module::instance($cm->id);

require_login($course, true, $cm);
require_capability('mod/videotrackerpro:viewreport', $context);

$PAGE->set_url('/mod/videotrackerpro/report.php', ['id' => $cm->id]);
$PAGE->set_context($context);
$PAGE->set_title(get_string('report', 'videotrackerpro'));
$PAGE->set_heading(format_string($activity->name));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('report', 'videotrackerpro'));
groups_print_activity_menu($cm, $PAGE->url);

if ($userid) {
    $user = $DB->get_record('user', ['id' => $userid], '*', MUST_EXIST);
    if (!is_enrolled($context, $user, 'mod/videotrackerpro:view', true)) {
        throw new moodle_exception('nopermissions', 'error');
    }
    if (groups_get_activity_groupmode($cm) == SEPARATEGROUPS
            && !has_capability('moodle/site:accessallgroups', $context)) {
        $groupid = groups_get_activity_group($cm, true);
        if (!$groupid || !groups_is_member($groupid, $userid)) {
            throw new moodle_exception('nopermissions', 'error');
        }
    }

    $data = manager::user($context, $activity, $userid);
    echo $OUTPUT->heading(fullname($user), 3);

    $table = new html_table();
    $table->head = [
        get_string('session', 'videotrackerpro'),
        get_string('started', 'videotrackerpro'),
        get_string('ended', 'videotrackerpro'),
        get_string('sessionduration', 'videotrackerpro'),
        get_string('watchtime', 'videotrackerpro'),
        get_string('pausedtime', 'videotrackerpro'),
        get_string('positions', 'videotrackerpro'),
        get_string('sessionprogress', 'videotrackerpro'),
        get_string('pauses', 'videotrackerpro'),
        get_string('seeks', 'videotrackerpro'),
        get_string('ratechanges', 'videotrackerpro'),
        get_string('speedavg', 'videotrackerpro'),
        get_string('reachedend', 'videotrackerpro'),
        get_string('endreason', 'videotrackerpro'),
    ];
    foreach ($data['sessions'] as $session) {
        $table->data[] = [
            s($session->sessionid),
            userdate((int)$session->startedat),
            (int)$session->endedat > 0 ? userdate((int)$session->endedat) : get_string('open', 'videotrackerpro'),
            format_time((int)($session->sessionduration ?? 0)),
            format_time((int)$session->watchtime),
            format_time((int)($session->pausedtime ?? 0)),
            format_time((int)($session->startposition ?? 0)) . ' → '
                . format_time((int)($session->endposition ?? $session->dropoff)),
            (int)($session->percentstart ?? 0) . '% → ' . (int)($session->percentend ?? 0) . '%',
            (int)$session->pauses,
            (int)$session->seeks,
            (int)($session->ratechanges ?? 0),
            format_float((float)$session->speedavg, 2) . 'x',
            !empty($session->receivedended) ? get_string('yes') : get_string('no'),
            get_string(
                'endreason:' . clean_param((string)($session->endreason ?? 'active_or_unclosed'), PARAM_ALPHANUMEXT),
                'videotrackerpro'
            ),
        ];
    }
    echo html_writer::table($table);

    foreach ($data['sessions'] as $session) {
        $events = json_decode((string)($session->events ?? ''), true) ?: [];
        $items = [];
        foreach ($events as $event) {
            if (!is_array($event)) {
                continue;
            }
            $type = (string)($event['type'] ?? '');
            $position = (float)($event['position'] ?? 0);
            $label = $type;
            if ($type === 'seek') {
                $label = get_string('eventseek', 'videotrackerpro', (object)[
                    'from' => format_time((int)($event['from'] ?? 0)),
                    'to' => format_time((int)($event['to'] ?? 0)),
                    'direction' => (string)($event['direction'] ?? ''),
                ]);
            } else if ($type === 'playbackrate') {
                $label = get_string('eventrate', 'videotrackerpro', (object)[
                    'from' => format_float((float)($event['from'] ?? 1), 2),
                    'to' => format_float((float)($event['to'] ?? 1), 2),
                ]);
            } else if ($type !== '') {
                $stringid = 'event' . $type;
                $label = get_string_manager()->string_exists($stringid, 'videotrackerpro')
                    ? get_string($stringid, 'videotrackerpro')
                    : $type;
            }
            $items[] = [
                'time' => format_time((int)$position),
                'label' => $label,
            ];
        }

        echo $OUTPUT->render_from_template('mod_videotrackerpro/timeline', [
            'sessionid' => s($session->sessionid),
            'started' => userdate((int)$session->startedat),
            'items' => $items,
        ]);
    }
} else {
    $table = new html_table();
    $table->head = [
        get_string('user'),
        get_string('percent', 'videotrackerpro'),
        get_string('watchtime', 'videotrackerpro'),
        get_string('sessions', 'videotrackerpro'),
        get_string('pauses', 'videotrackerpro'),
        get_string('seeks', 'videotrackerpro'),
        get_string('speedavg', 'videotrackerpro'),
        get_string('lastpoint', 'videotrackerpro'),
        get_string('lastview', 'videotrackerpro'),
        get_string('completion', 'completion'),
    ];

    foreach (manager::users($context, $activity) as $row) {
        $url = new moodle_url('/mod/videotrackerpro/report.php', ['id' => $cm->id, 'userid' => $row['user']->id]);
        $table->data[] = [
            html_writer::link($url, fullname($row['user'])),
            $row['percent'] . '%',
            format_time($row['watchtime']),
            $row['sessions'],
            $row['pauses'],
            $row['seeks'],
            $row['speedavg'] ? format_float($row['speedavg'], 2) . 'x' : '—',
            format_time($row['currenttime']),
            $row['lastview'] ? userdate($row['lastview']) : '—',
            $row['complete'] ? get_string('yes') : get_string('no'),
        ];
    }
    echo html_writer::table($table);
}

echo $OUTPUT->footer();
