<?php

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/blocks/olympiads/lib.php');

// Получаем ID олимпиады из URL
$id = required_param('id', PARAM_INT);

// Проверка прав доступа: только сотрудники приемной комиссии могут просматривать список
require_login();
$context = context_system::instance();
require_capability('block/olympiads:manage', $context);

// Получаем запись об олимпиаде, чтобы вывести ее название
$olympiad = $DB->get_record('block_olympiads', ['id' => $id], '*', MUST_EXIST);

// Устанавливаем заголовок и контекст страницы
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/blocks/olympiads/participants.php', ['id' => $id]));
$PAGE->set_title(get_string('participantslist', 'block_olympiads', $olympiad->name));
$PAGE->set_heading(get_string('participantslist', 'block_olympiads', $olympiad->name));

echo $OUTPUT->header();

// Используем Moodle Database API для получения данных
$participants_records = $DB->get_records('block_olympiads_participants', ['olympiadid' => $id]);

if (empty($participants_records)) {
    echo $OUTPUT->box(get_string('noparticipants', 'block_olympiads'), 'empty');
} else {
    // Получаем список ID пользователей
    $userids = array_column($participants_records, 'userid');

    // Получаем информацию о пользователях
    $users = $DB->get_records_list('user', 'id', $userids);

    $table = new html_table();
    $table->head = [
        get_string('fullname'),
        get_string('email'),
        get_string('enrolmentdate', 'block_olympiads')
    ];
    $table->align = ['left', 'left', 'center'];
    $table->data = [];

    foreach ($participants_records as $participant_record) {
        $user = $users[$participant_record->userid];
        $row = [
            fullname($user),
            $user->email,
            userdate($participant_record->timecreated)
        ];
        $table->data[] = $row;
    }
    echo html_writer::table($table);
}

// Кнопка "Назад"
$backurl = new moodle_url('/blocks/olympiads/view.php');
echo $OUTPUT->single_button($backurl, get_string('back', 'moodle'), 'get');

echo $OUTPUT->footer();