<?php

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/blocks/olympiads/lib.php');

// Получаем ID олимпиады из URL
$id = required_param('id', PARAM_INT);

// Проверка прав доступа
require_login();
$context = context_system::instance();
require_capability('block/olympiads:subscribe', $context);

// Получаем запись об олимпиаде
$olympiad = $DB->get_record('block_olympiads', ['id' => $id], '*', MUST_EXIST);

// Устанавливаем заголовок и контекст страницы
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/blocks/olympiads/view.php', ['id' => $id]));
$PAGE->set_title($olympiad->name);
$PAGE->set_heading($olympiad->name);

// Проверяем, записан ли абитуриент на эту олимпиаду
$is_enrolled = $DB->record_exists('block_olympiads_participants', ['olympiadid' => $olympiad->id, 'userid' => $USER->id]);

// Создаем URL для кнопки "Записаться"
$subscribe_url = new moodle_url('/blocks/olympiads/subscribe.php', ['id' => $olympiad->id]);

// Подготавливаем данные для шаблона
$data = [
    'name' => $olympiad->name,
    'description' => format_text($olympiad->description),
    'startdate' => userdate($olympiad->startdate),
    'enddate' => userdate($olympiad->enddate),
    'is_enrolled' => $is_enrolled,
    'subscribe_url' => $subscribe_url,
    'image' => isset($olympiad->image) ? file_safe_url($olympiad->image, 'block_olympiads') : null,
    'str' => [
        'startdate' => get_string('startdate', 'block_olympiads'),
        'enddate' => get_string('enddate', 'block_olympiads'),
        'subscribe' => get_string('subscribe', 'block_olympiads'),
        'alreadyenrolled' => get_string('alreadyenrolled', 'block_olympiads')
    ]
];

echo $OUTPUT->header();

// Рендерим шаблон
echo $OUTPUT->render_from_template('block_olympiads/olympiad_view', $data);

echo $OUTPUT->footer();