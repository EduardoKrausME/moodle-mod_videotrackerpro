<?php
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

if ($userid) {
    $user = $DB->get_record('user', ['id' => $userid], '*', MUST_EXIST);
    if (!is_enrolled($context, $user, 'mod/videotrackerpro:view', true)) {
        throw new moodle_exception('nopermissions', 'error');
    }

    $data = manager::user($context, $activity, $userid);
    echo $OUTPUT->heading(fullname($user), 3);

    $table = new html_table();
    $table->head = [
        get_string('session', 'videotrackerpro'),
        get_string('started', 'videotrackerpro'),
        get_string('ended', 'videotrackerpro'),
        get_string('watchtime', 'videotrackerpro'),
        get_string('pauses', 'videotrackerpro'),
        get_string('seeks', 'videotrackerpro'),
        get_string('speedavg', 'videotrackerpro'),
        get_string('dropoff', 'videotrackerpro'),
    ];
    foreach ($data['sessions'] as $session) {
        $table->data[] = [
            s($session->sessionid),
            userdate((int)$session->startedat),
            (int)$session->endedat > 0 ? userdate((int)$session->endedat) : get_string('open', 'videotrackerpro'),
            format_time((int)$session->watchtime),
            (int)$session->pauses,
            (int)$session->seeks,
            format_float((float)$session->speedavg, 2) . 'x',
            format_time((int)$session->dropoff),
        ];
    }
    echo html_writer::table($table);

    foreach ($data['sessions'] as $session) {
        echo $OUTPUT->render_from_template('mod_videotrackerpro/timeline', [
            'sessionid' => s($session->sessionid),
            'started' => userdate((int)$session->startedat),
            'events' => json_encode([
                'pausepoints' => json_decode((string)$session->pausepoints, true) ?: [],
                'forward_seeks' => json_decode((string)$session->skippoints, true) ?: [],
                'backward_seeks' => json_decode((string)$session->replaypoints, true) ?: [],
                'rates' => json_decode((string)$session->rates, true) ?: [],
                'dropoff' => (int)$session->dropoff,
                'ended' => (bool)$session->endedat,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
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
