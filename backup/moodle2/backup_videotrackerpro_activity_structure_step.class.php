<?php
class backup_videotrackerpro_activity_structure_step extends backup_activity_structure_step {
    protected function define_structure() {
        $root = new backup_nested_element('videotrackerpro', ['id'], [
            'name', 'intro', 'introformat', 'videosource', 'sourceconfig', 'videourl',
            'completionpercent', 'recordsessions', 'recordseeks', 'recordpauses',
            'recordrates', 'recordbuffering', 'recorddropoff', 'showpersonal',
            'timecreated', 'timemodified',
        ]);
        $root->set_source_table('videotrackerpro', ['id' => backup::VAR_ACTIVITYID]);
        $root->annotate_files('mod_videotrackerpro', 'intro', null);
        return $this->prepare_activity_structure($root);
    }
}
