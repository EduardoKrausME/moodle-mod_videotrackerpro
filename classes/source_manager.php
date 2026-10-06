<?php
namespace mod_videotrackerpro;

final class source_manager {
    public static function create(): \local_video_bridge\source\manager {
        return new \local_video_bridge\source\manager('videosource', 'sourceconfig', 'videourl');
    }
}
