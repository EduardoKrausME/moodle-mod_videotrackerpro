<?php
defined('MOODLE_INTERNAL') || die;

$observers = [
    [
        'eventname' => '\\local_video_bridge\\event\\analytics_updated',
        'callback' => '\\mod_videotrackerpro\\observer::analytics_updated',
    ],
];
