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
 * mod_form.php
 *
 * @package   mod_videotrackerpro
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_videotrackerpro\source_manager;

defined('MOODLE_INTERNAL') || die;
require_once($CFG->dirroot . '/course/moodleform_mod.php');

/**
 * Class mod_videotrackerpro_mod_form.
 */
class mod_videotrackerpro_mod_form extends moodleform_mod {
    /**
     * Method definition.
     *
     * @return void Return value.
     */
    public function definition(): void {
        $mform = $this->_form;
        $bridge = source_manager::create();

        $mform->addElement('header', 'general', get_string('general', 'form'));
        $mform->addElement('text', 'name', get_string('videotrackerproname', 'videotrackerpro'), ['size' => 64]);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $this->standard_intro_elements();

        $mform->addElement('header', 'videosourceheader', get_string('videosourceheader', 'videotrackerpro'));
        $options = $bridge->get_options(['tracking']);
        $mform->addElement('select', 'videosource', get_string('videosource', 'videotrackerpro'), $options);
        if ($options) {
            $mform->setDefault('videosource', array_key_first($options));
        }
        $bridge->add_form_elements($mform, 'videosource');

        $mform->addElement('header', 'analyticsheader', get_string('analyticsheader', 'videotrackerpro'));
        foreach (['recordsessions','recordseeks','recordpauses','recordrates','recordbuffering','recorddropoff','showpersonal'] as $field) {
            $mform->addElement('advcheckbox', $field, get_string($field, 'videotrackerpro'));
            $mform->setDefault($field, 1);
        }

        $this->standard_coursemodule_elements();
        $this->add_action_buttons();
    }

    /**
     * Method add_completion_rules.
     *
     * @return array Return value.
     */
    public function add_completion_rules(): array {
        $mform = $this->_form;
        $mform->addElement('text', 'completionpercent', get_string('completionpercent', 'videotrackerpro'), ['size' => 4]);
        $mform->setType('completionpercent', PARAM_INT);
        $mform->setDefault('completionpercent', 0);
        return ['completionpercent'];
    }

    /**
     * Method completion_rule_enabled.
     *
     * @param mixed $data Parameter data.
     * @return bool Return value.
     */
    public function completion_rule_enabled($data): bool {
        return (int)($data['completionpercent'] ?? 0) > 0;
    }

    /**
     * Method data_preprocessing.
     *
     * @param mixed $defaultvalues Parameter defaultvalues.
     * @return void Return value.
     */
    public function data_preprocessing(&$defaultvalues): void {
        parent::data_preprocessing($defaultvalues);
        if (!empty($this->current->id) && !empty($this->context)) {
            source_manager::create()->prepare_form_data($defaultvalues, $this->context);
        }
    }

    /**
     * Method validation.
     *
     * @param mixed $data Parameter data.
     * @param mixed $files Parameter files.
     * @return array Return value.
     */
    public function validation($data, $files): array {
        $errors = parent::validation($data, $files);
        $errors += source_manager::create()->validation($data, $files);
        $percent = (int)($data['completionpercent'] ?? 0);
        if ($percent < 0 || $percent > 100) {
            $errors['completionpercent'] = get_string('errorpercent', 'videotrackerpro');
        }
        return $errors;
    }
}
