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
 * backup_videotrackerpro_stepslib.php
 *
 * @package   mod_videotrackerpro
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_videotrackerpro_activity_structure_step extends backup_activity_structure_step {
    /**
     * Method define_structure.
     *
     * @return mixed Return value.
     */
    protected function define_structure() {
        $root = new backup_nested_element('videotrackerpro', ['id'], [
            'name', 'intro', 'introformat', 'videosource', 'sourceconfig', 'videourl',
            'completionpercent', 'recordsessions', 'recordseeks', 'recordpauses',
            'recordrates', 'recordbuffering', 'recorddropoff', 'showpersonal',
            'timecreated', 'timemodified',
        ]);
        $root->set_source_table('videotrackerpro', ['id' => backup::VAR_ACTIVITYID]);
        $root->annotate_files('mod_videotrackerpro', 'intro', null);
        $root->annotate_files('local_video_bridge', 'video', 0);
        return $this->prepare_activity_structure($root);
    }
}
