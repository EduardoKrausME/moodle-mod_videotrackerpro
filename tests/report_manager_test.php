<?php
namespace mod_videotrackerpro;

final class report_manager_test extends \advanced_testcase {
    public function test_mediahash_is_stable(): void {
        $activity = (object)['videosource' => 'html5', 'sourceconfig' => '{"url":"x"}'];
        $this->assertSame(
            \local_video_bridge\progress\manager::media_hash('html5', '{"url":"x"}'),
            \mod_videotrackerpro\report\manager::mediahash($activity)
        );
    }
}
