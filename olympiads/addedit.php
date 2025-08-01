<?php
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/blocks/olympiads/lib.php');
require_once($CFG->dirroot . '/blocks/olympiads/classes/form/olympiad_form.php');

// Проверка прав доступа
require_login();
require_capability('block/olympiads:manage', context_system::instance());

$id = optional_param('id', 0, PARAM_INT);

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/blocks/olympiads/addedit.php', ['id' => $id]);
$PAGE->set_title(get_string('addeditolympiad', 'block_olympiads'));
$PAGE->set_heading(get_string('addeditolympiad', 'block_olympiads'));

$form = new \block_olympiads\form\olympiad_form(new moodle_url('/blocks/olympiads/addedit.php'), ['olympiadid' => $id]);

if ($form->is_cancelled()) {
    redirect(new moodle_url('/blocks/olympiads/view.php'));
} else if ($fromform = $form->get_data()) {
    if (isset($fromform->description['text'])) {
        $fromform->description = $fromform->description['text'];
    }

    if ($id) {
        $fromform->id = $id;
        $fromform->timemodified = time();
        $fromform->usermodified = $USER->id;
        $DB->update_record('block_olympiads', $fromform);
    } else {
        $fromform->timecreated = time();
        $fromform->timemodified = time();
        $fromform->usermodified = $USER->id;
        $id = $DB->insert_record('block_olympiads', $fromform);
    }

    redirect(new moodle_url('/my/'), get_string('savedsuccessfully', 'block_olympiads'));
} else if ($id > 0) {
    $olympiad = $DB->get_record('block_olympiads', ['id' => $id]);
    if ($olympiad) {
        $form->set_data($olympiad);
    } else {
        redirect(new moodle_url('/blocks/olympiads/addedit.php'), get_string('olympiadnotfound', 'block_olympiads'), null, \core\output\notification::NOTIFY_WARNING);
    }
}

echo $OUTPUT->header();
$form->display();
echo $OUTPUT->footer();