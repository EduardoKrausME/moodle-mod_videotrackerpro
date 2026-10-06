<?php
class backup_videotrackerpro_activity_task extends backup_activity_task {
    protected function define_my_settings() {}
    protected function define_my_steps() {
        $this->add_step(new backup_videotrackerpro_activity_structure_step(
            'videotrackerpro_structure',
            'videotrackerpro.xml'
        ));
    }
    public static function encode_content_links($content) {
        return $content;
    }
}
