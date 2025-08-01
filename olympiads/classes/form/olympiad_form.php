<?php
namespace block_olympiads\form;

require_once($CFG->libdir . '/formslib.php');

class olympiad_form extends \moodleform {
    protected function definition() {
        global $CFG, $DB;

        $mform = $this->_form;

        $mform->addElement('header', 'general', get_string('general', 'block_olympiads'));

        $mform->addElement('text', 'name', get_string('name', 'block_olympiads'), ['size' => '60']);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', get_string('required'), 'required', null, 'client');
        $mform->addRule('name', get_string('maxlength', 'block_olympiads', 255), 'maxlength', 255, 'client');

        $editoroptions = array(
            'subdirs' => 0,
            'maxbytes' => 0,
            'maxfiles' => 0,
            'changeformat' => 1,
            'context' => null
        );

        $mform->addElement('editor', 'description', get_string('description', 'block_olympiads'), $editoroptions);
        $mform->setType('description', PARAM_RAW);
        $mform->addRule('description', get_string('required'), 'required', null, 'client');

        $mform->addElement('date_selector', 'startdate', get_string('startdate', 'block_olympiads'), ['startyear' => date('Y'), 'stopyear' => date('Y') + 5]);
        $mform->setType('startdate', PARAM_INT);
        $mform->addRule('startdate', get_string('required'), 'required', null, 'client');

        $mform->addElement('date_selector', 'enddate', get_string('enddate', 'block_olympiads'), ['startyear' => date('Y'), 'stopyear' => date('Y') + 5]);
        $mform->setType('enddate', PARAM_INT);
        $mform->addRule('enddate', get_string('required'), 'required', null, 'client');

        $mform->addRule('enddate', get_string('enddatemustbeafterstartdate', 'block_olympiads'), 'callback', 'validate_dates', 'client', [$mform], 'startdate');

        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);

        $this->add_action_buttons(true, get_string('savechanges', 'block_olympiads'));
    }
}