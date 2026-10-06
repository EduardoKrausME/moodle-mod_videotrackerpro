<?php
require('../../config.php');

$id = required_param('id', PARAM_INT);
$course = $DB->get_record('course', ['id' => $id], '*', MUST_EXIST);
require_course_login($course);

$PAGE->set_url('/mod/videotrackerpro/index.php', ['id' => $id]);
$PAGE->set_title(get_string('pluginname', 'videotrackerpro'));
$PAGE->set_heading(format_string($course->fullname));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'videotrackerpro'));

$table = new html_table();
$table->head = [get_string('name')];
foreach (get_all_instances_in_course('videotrackerpro', $course) as $activity) {
    $table->data[] = [html_writer::link(
        new moodle_url('/mod/videotrackerpro/view.php', ['id' => $activity->coursemodule]),
        format_string($activity->name)
    )];
}
echo html_writer::table($table);
echo $OUTPUT->footer();
