<?php
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/blocks/olympiads/lib.php');

// Проверка прав доступа
require_login();
require_capability('moodle/site:manageblocks', context_system::instance());

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/blocks/olympiads/view.php'));
$PAGE->set_title(get_string('olympiads', 'block_olympiads'));
$PAGE->set_heading(get_string('olympiads', 'block_olympiads'));

echo $OUTPUT->header();

echo html_writer::tag('h2', get_string('manageolympiads', 'block_olympiads'));

$addurl = new moodle_url('/blocks/olympiads/addedit.php');
echo $OUTPUT->single_button($addurl, get_string('addolympiad', 'block_olympiads'));


$olympiads = $DB->get_records('block_olympiads', null, 'name ASC');

if (empty($olympiads)) {
    echo $OUTPUT->box(get_string('noolympiads', 'block_olympiads'), 'empty');
} else {
    $table = new html_table();

    // Заголовки таблицы
    $table->head = [
        get_string('name', 'block_olympiads'),
        get_string('startdate', 'block_olympiads'),
        get_string('enddate', 'block_olympiads'),
        get_string('actions', 'block_olympiads') // Заголовок для колонок с действиями
    ];

    $table->align = ['left', 'center', 'center', 'center'];

    $table->data = []; // Массив для строк с данными

    foreach ($olympiads as $olympiad) {
        $editurl = new moodle_url('/blocks/olympiads/addedit.php', ['id' => $olympiad->id]);
        $deleteurl = new moodle_url('/blocks/olympiads/delete.php', ['id' => $olympiad->id]);

        // Кнопка "Редактировать"
        $edit_icon = html_writer::link($editurl,
            html_writer::tag('i', '', ['class' => 'icon fa fa-pencil fa-fw', 'aria-hidden' => 'true']),
            ['title' => get_string('edit')]
        );

        // Кнопка "Удалить"
        $delete_icon = html_writer::link($deleteurl,
            html_writer::tag('i', '', ['class' => 'icon fa fa-trash fa-fw', 'aria-hidden' => 'true']),
            ['title' => get_string('delete')]
        );

        $actions = $edit_icon . ' ' . $delete_icon;

        $row = [
            $olympiad->name,
            userdate($olympiad->startdate),
            userdate($olympiad->enddate),
            $actions
        ];

        $table->data[] = $row;
    }

    echo html_writer::table($table);
}

echo $OUTPUT->footer();