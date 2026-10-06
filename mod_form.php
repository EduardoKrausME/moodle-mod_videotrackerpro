<?php
use mod_videotrackerpro\source_manager;

defined('MOODLE_INTERNAL') || die;
require_once($CFG->dirroot . '/course/moodleform_mod.php');

class mod_videotrackerpro_mod_form extends moodleform_mod {
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

    public function add_completion_rules(): array {
        $mform = $this->_form;
        $mform->addElement('text', 'completionpercent', get_string('completionpercent', 'videotrackerpro'), ['size' => 4]);
        $mform->setType('completionpercent', PARAM_INT);
        $mform->setDefault('completionpercent', 0);
        return ['completionpercent'];
    }

    public function completion_rule_enabled($data): bool {
        return (int)($data['completionpercent'] ?? 0) > 0;
    }

    public function data_preprocessing(&$defaultvalues): void {
        parent::data_preprocessing($defaultvalues);
        if (!empty($this->current->id) && !empty($this->context)) {
            source_manager::create()->prepare_form_data($defaultvalues, $this->context);
        }
    }

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
