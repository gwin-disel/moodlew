<?php

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/blocks/olympiads/lib.php');

// Проверяем, что пользователь авторизован и имеет права на управление
require_login();
$context = context_system::instance();
require_capability('block/olympiads:manage', $context);

// Получаем ID олимпиады из URL
$id = required_param('id', PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_INT);

// Получаем запись об олимпиаде, чтобы показать её название в сообщении
$olympiad = $DB->get_record('block_olympiads', ['id' => $id]);

// Если олимпиада не найдена, перенаправляем на страницу со списком
if (!$olympiad) {
    redirect(new moodle_url('/blocks/olympiads/view.php'), get_string('olympiadnotfound', 'block_olympiads'), null, \core\output\notification::NOTIFY_WARNING);
}

// Устанавливаем заголовок и контекст страницы
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/blocks/olympiads/delete.php', ['id' => $id]));
$PAGE->set_title(get_string('deleteolympiad', 'block_olympiads'));
$PAGE->set_heading(get_string('deleteolympiad', 'block_olympiads'));

// Если пользователь подтвердил удаление
if ($confirm) {
    // 1. Сначала удаляем все записи об участниках этой олимпиады из дочерней таблицы.
    // Это предотвратит ошибку foreign key constraint.
    $DB->delete_records('block_olympiads_participants', ['olympiadid' => $id]);

    // 2. Затем удаляем саму олимпиаду из родительской таблицы
    $DB->delete_records('block_olympiads', ['id' => $id]);

    // Перенаправляем обратно на страницу со списком олимпиад с сообщением об успехе
    redirect(new moodle_url('/my/'), get_string('deletesuccessfully', 'block_olympiads'));
}

// Если удаление не подтверждено, показываем страницу с запросом подтверждения
echo $OUTPUT->header();

$message = get_string('confirmdelete', 'block_olympiads', $olympiad->name);
$confirm_url = new moodle_url('/blocks/olympiads/delete.php', ['id' => $id, 'confirm' => 1, 'sesskey' => sesskey()]);
$cancel_url = new moodle_url('/blocks/olympiads/view.php');

echo $OUTPUT->confirm($message, $confirm_url, $cancel_url);

echo $OUTPUT->footer();