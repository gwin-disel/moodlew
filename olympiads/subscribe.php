<?php

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/blocks/olympiads/lib.php');

require_login();
$context = context_system::instance();
require_capability('block/olympiads:subscribe', $context);

$olympiadid = required_param('id', PARAM_INT);

$olympiad = $DB->get_record('block_olympiads', ['id' => $olympiadid]);

if (!$olympiad) {
    redirect(new moodle_url('/my/'), get_string('olympiadnotfound', 'block_olympiads'), null, \core\output\notification::NOTIFY_ERROR);
}

// Проверяем, не записан ли пользователь уже на эту олимпиаду
$is_enrolled = $DB->record_exists('block_olympiads_participants', ['olympiadid' => $olympiadid, 'userid' => $USER->id]);

if ($is_enrolled) {
    redirect(new moodle_url('/my/'), get_string('alreadyenrolled', 'block_olympiads'), null, \core\output\notification::NOTIFY_WARNING);
}

$record = new stdClass();
$record->olympiadid = $olympiadid;
$record->userid = $USER->id;
$record->timecreated = time();

$DB->insert_record('block_olympiads_participants', $record);

redirect(new moodle_url('/my/'), get_string('subscribesuccessfully', 'block_olympiads'));