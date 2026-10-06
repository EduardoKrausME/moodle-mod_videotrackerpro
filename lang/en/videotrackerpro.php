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
 * videotrackerpro.php
 *
 * @package   mod_videotrackerpro
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$string['analyticsheader'] = 'Analytics';
$string['completionpercent'] = 'Minimum watched percentage for completion';
$string['dropoff'] = 'Session end point';
$string['ended'] = 'Ended';
$string['endreason'] = 'Session end reason';
$string['endreason:active_or_unclosed'] = 'Session still active or no closing signal was received';
$string['endreason:closed_before_end'] = 'Session closed before the video ended';
$string['endreason:ended'] = 'Video ended normally';
$string['endreason:page_closed_or_navigated'] = 'Page closed or navigated away before the video ended';
$string['errorpercent'] = 'Enter a value between 0 and 100.';
$string['errortrackingrequired'] = 'The selected Video Bridge source does not provide reliable tracking.';
$string['eventended'] = 'ended';
$string['eventpause'] = 'pause';
$string['eventplay'] = 'play';
$string['eventplaying'] = 'playing';
$string['eventrate'] = 'rate {$a->from}x → {$a->to}x';
$string['eventseek'] = 'seek {$a->from} → {$a->to} ({$a->direction})';
$string['eventsessionend'] = 'session end';
$string['eventsessionstart'] = 'session start';
$string['eventvisibilitychange'] = 'visibility changed';
$string['eventwaiting'] = 'buffering';
$string['lastpoint'] = 'Last point';
$string['lastview'] = 'Last view';
$string['modulename'] = 'Video Tracker Pro';
$string['myanalytics'] = 'My analytics';
$string['noevents'] = 'No ordered events recorded for this session.';
$string['open'] = 'Open';
$string['pausedtime'] = 'Paused time';
$string['pauses'] = 'Pauses';
$string['percent'] = 'Watched';
$string['pluginadministration'] = 'Video Tracker Pro administration';
$string['pluginname'] = 'Video Tracker Pro';
$string['positions'] = 'Start → end position';
$string['privacy:metadata'] = 'Video Tracker Pro stores activity configuration only. Playback progress and session telemetry are stored by local_video_bridge for this activity context.';
$string['privacy:metadata:bridge'] = 'Video Bridge stores normalized progress and compact session telemetry for Video Tracker Pro.';
$string['ratechanges'] = 'Rate changes';
$string['reachedend'] = 'Ended event received';
$string['recordbuffering'] = 'Record buffering events';
$string['recorddropoff'] = 'Record session end before video end';
$string['recordpauses'] = 'Record pauses';
$string['recordrates'] = 'Record playback-rate changes';
$string['recordseeks'] = 'Record seeks';
$string['recordsessions'] = 'Record sessions';
$string['report'] = 'Playback analytics';
$string['seeks'] = 'Seeks';
$string['session'] = 'Session';
$string['sessionduration'] = 'Session duration';
$string['sessionprogress'] = 'Progress at session start → end';
$string['sessions'] = 'Sessions';
$string['sessiontimeline'] = 'Session timeline';
$string['showpersonal'] = 'Show personal analytics to learner';
$string['speedavg'] = 'Average speed';
$string['started'] = 'Started';
$string['videosource'] = 'Video source';
$string['videosourceheader'] = 'Video source';
$string['videotrackerpro:addinstance'] = 'Add a Video Tracker Pro activity';
$string['videotrackerpro:view'] = 'View Video Tracker Pro';
$string['videotrackerpro:viewreport'] = 'View learner playback analytics';
$string['videotrackerproname'] = 'Name';
$string['viewreport'] = 'View analytics report';
$string['watchtime'] = 'Active playback time';
