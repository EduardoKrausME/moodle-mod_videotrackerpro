<?php
class restore_videotrackerpro_activity_structure_step extends restore_activity_structure_step {
    protected function define_structure() {
        return [new restore_path_element('videotrackerpro', '/activity/videotrackerpro')];
    }
    protected function process_videotrackerpro($data) {
        global $DB;
        $data = (object)$data;
        $data->course = $this->get_courseid();
        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);
        $newid = $DB->insert_record('videotrackerpro', $data);
        $this->apply_activity_instance($newid);
    }
    protected function after_execute() {
        $this->add_related_files('mod_videotrackerpro', 'intro', null);
    }
}
