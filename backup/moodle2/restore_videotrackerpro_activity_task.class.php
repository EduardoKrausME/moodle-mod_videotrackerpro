<?php
class restore_videotrackerpro_activity_task extends restore_activity_task {
    protected function define_my_settings() {}
    protected function define_my_steps() {
        $this->add_step(new restore_videotrackerpro_activity_structure_step(
            'videotrackerpro_structure',
            'videotrackerpro.xml'
        ));
    }
    public static function define_decode_contents() {
        return [new restore_decode_content('videotrackerpro', ['intro'], 'videotrackerpro')];
    }
    public static function define_decode_rules() {
        return [];
    }
}
