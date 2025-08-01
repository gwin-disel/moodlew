<?php

defined('MOODLE_INTERNAL') || die();

class block_olympiads extends block_base
{
    public function init() {
        $this->title = get_string('pluginname', 'block_olympiads');
    }

    public function get_content() {
        global $DB, $USER, $OUTPUT, $PAGE;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';

        $canmanage = has_capability('block/olympiads:manage', context_system::instance());
//        $canmanage = true;

        if ($canmanage) {
            // КОНТЕНТ ДЛЯ СОТРУДНИКА (С ПРАВАМИ УПРАВЛЕНИЯ)
            $this->content->text .= html_writer::tag('h4', get_string('manageolympiads', 'block_olympiads'));

            $addurl = new moodle_url('/blocks/olympiads/addedit.php');
            $this->content->text .= $OUTPUT->single_button($addurl, get_string('addolympiad', 'block_olympiads'));

            $olympiads = $DB->get_records('block_olympiads', null, 'name ASC');

            if (empty($olympiads)) {
                $this->content->text .= $OUTPUT->box(get_string('noolympiads', 'block_olympiads'), 'empty');
            } else {
                $table = new html_table();
                $table->head = [
                    get_string('name', 'block_olympiads'),
                    get_string('startdate', 'block_olympiads'),
                    get_string('enddate', 'block_olympiads'),
                    get_string('actions', 'block_olympiads')
                ];
                $table->align = ['left', 'center', 'center', 'center'];
                $table->data = [];

                foreach ($olympiads as $olympiad) {
                    $editurl = new moodle_url('/blocks/olympiads/addedit.php', ['id' => $olympiad->id]);
                    $deleteurl = new moodle_url('/blocks/olympiads/delete.php', ['id' => $olympiad->id]);
                    $participantsurl = new moodle_url('/blocks/olympiads/participants.php', ['id' => $olympiad->id]);

                    $edit_icon = html_writer::link($editurl,
                        html_writer::tag('i', '', ['class' => 'icon fa fa-pencil fa-fw', 'aria-hidden' => 'true']),
                        ['title' => get_string('edit')]
                    );
                    $delete_icon = html_writer::link($deleteurl,
                        html_writer::tag('i', '', ['class' => 'icon fa fa-trash fa-fw', 'aria-hidden' => 'true']),
                        ['title' => get_string('delete')]
                    );
                    $actions = $edit_icon . ' ' . $delete_icon;

                    // Измененная строка: название олимпиады теперь является ссылкой на страницу участников
                    $linked_name = html_writer::link($participantsurl, $olympiad->name);

                    $row = [
                        $linked_name,
                        userdate($olympiad->startdate),
                        userdate($olympiad->enddate),
                        $actions
                    ];
                    $table->data[] = $row;
                }
                $this->content->text .= html_writer::table($table);
            }

        } else {
            // КОНТЕНТ ДЛЯ АБИТУРИЕНТА
            $olympiads = $DB->get_records('block_olympiads', null, 'startdate ASC');
            $data = [];

            if (!empty($olympiads)) {
                foreach ($olympiads as $olympiad) {
                    // Проверяем, записан ли абитуриент на эту олимпиаду
                    $is_enrolled_on_olympiad = $DB->record_exists('block_olympiads_participants', ['olympiadid' => $olympiad->id, 'userid' => $USER->id]);

                    $viewurl = new moodle_url('/blocks/olympiads/view.php', ['id' => $olympiad->id]);
                    $image = isset($olympiad->image) ? file_safe_url($olympiad->image, 'block_olympiads') : null;

                    $data[] = [
                        'name' => $olympiad->name,
                        'description' => $olympiad->description,
                        'startdate' => userdate($olympiad->startdate),
                        'enddate' => userdate($olympiad->enddate),
                        'is_enrolled' => $is_enrolled_on_olympiad,
                        'subscribe_url' => new moodle_url('/blocks/olympiads/subscribe.php', ['id' => $olympiad->id]),
                        'view_url' => $viewurl,
                        'image' => $image,
                    ];
                }
            }

            // Рендерим шаблон student_list
            $this->content->text = $OUTPUT->render_from_template('block_olympiads/student_list', [
                'olympiads' => $data,
                'str' => [
                    'title' => get_string('olympiads', 'block_olympiads'),
                    'startdate' => get_string('startdate', 'block_olympiads'),
                    'enddate' => get_string('enddate', 'block_olympiads'),
                    'subscribe' => get_string('subscribe', 'block_olympiads'),
                    'enrolled' => get_string('enrolled', 'block_olympiads'),
                    'noolympiads' => get_string('noolympiads', 'block_olympiads'),
                ]
            ]);
        }

        return $this->content;
    }
}