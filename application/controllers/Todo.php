<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Todo extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Todo_model');
        $this->load->model('Subtask_model');
        $this->load->helper(['form', 'url']);
        $this->load->library('form_validation');
    }

    // ============================================
    // HALAMAN UTAMA
    // ============================================
    public function index() {
        $data['todos'] = $this->Todo_model->get_all();

        foreach ($data['todos'] as $t) {
            $t->subtasks = $this->Subtask_model->get_by_todo($t->id);
        }

        $this->load->view('templates/header');
        $this->load->view('todo/index', $data);
        $this->load->view('templates/footer');
    }

    // ============================================
    // CREATE
    // ============================================
    public function create() {
        $this->load->view('templates/header');
        $this->load->view('todo/form');
        $this->load->view('templates/footer');
    }

    public function store()
{
    $this->form_validation->set_rules('title', 'Title', 'required');
    $this->form_validation->set_rules('priority', 'Priority', 'required|in_list[low,medium,high]');

    if ($this->form_validation->run() == FALSE) {
        $this->load->view('todo/form');
        return;
    }

    // SIMPAN TODO
    $todoData = [
        'title'      => $this->input->post('title', true),
        'priority'   => $this->input->post('priority', true),
        'is_done'    => 0,
        'created_at'=> date('Y-m-d H:i:s')
    ];

    $todo_id = $this->Todo_model->insert($todoData);

    // SIMPAN SUBTASK
    $subtasks = $this->input->post('subtask');
    if ($subtasks) {
        foreach ($subtasks as $title) {
            if (trim($title) !== '') {
                $this->Todo_model->insertSubtask([
                    'todo_id' => $todo_id,
                    'title'   => $title
                ]);
            }
        }
    }

    redirect('todo');
}

    // ============================================
    // EDIT / UPDATE
    // ============================================
    public function update($id)
{
    $this->form_validation->set_rules('title', 'Title', 'required');
    $this->form_validation->set_rules('priority', 'Priority', 'required|in_list[low,medium,high]');

    if ($this->form_validation->run() == FALSE) {
        $data['todo'] = $this->Todo_model->getById($id);
        $this->load->view('todo/form', $data);
        return;
    }

    $data = [
        'title'    => $this->input->post('title', true),
        'priority' => $this->input->post('priority', true)
    ];

    $this->Todo_model->update($id, $data);

    redirect('todo');
}

    // ============================================
    // DELETE
    // ============================================
    public function delete($id = NULL) {
        if (!$id) show_404();

        $this->Subtask_model->delete_by_todo($id);
        $this->Todo_model->delete($id);

        redirect('todo');
    }


    // ============================================
    // TOGGLE
    // ============================================
    public function toggle($id = NULL) {
        if (!$id) show_404();

        $todo = $this->Todo_model->get($id);
        if (!$todo) show_404();

        $this->Todo_model->update($id, [
            'is_done' => ($todo->is_done ? 0 : 1)
        ]);

        redirect('todo');
    }


    // ============================================
    // DETAIL
    // ============================================
    public function detail($id) {
        $data['todo'] = $this->Todo_model->get($id);
        if (!$data['todo']) show_404();

        $data['subtasks'] = $this->Subtask_model->get_by_todo($id);

        $this->load->view('templates/header');
        $this->load->view('todo/detail', $data);
        $this->load->view('templates/footer');
    }


    // ============================================
    // SUBTASK CRUD
    // ============================================
    public function add_subtask($todo_id) {
        $title = trim($this->input->post('title'));

        if ($title !== "") {
            $this->Subtask_model->insert([
                'todo_id' => $todo_id,
                'title'   => $title,
                'is_done' => 0
            ]);
        }

        redirect('todo/detail/'.$todo_id);
    }

    public function toggle_subtask($id, $todo_id) {
        $this->Subtask_model->toggle($id);
        redirect('todo/detail/'.$todo_id);
    }

    public function delete_subtask($id, $todo_id) {
        $this->Subtask_model->delete($id);
        redirect('todo/detail/'.$todo_id);
    }

    public function edit_subtask($id, $todo_id) {
        $title = trim($this->input->post('title'));

        if ($title !== "") {
            $this->Subtask_model->update($id, ['title' => $title]);
        }

        redirect('todo/detail/'.$todo_id);
    }

    // ============================================
    // AJAX (FIXED – NOW INSIDE CLASS)
    // ============================================
    public function ajax_add_subtask() {
        $todo_id = $this->input->post('todo_id');
        $title   = $this->input->post('title');

        $id = $this->Subtask_model->insert([
            'todo_id' => $todo_id,
            'title'   => $title,
            'is_done' => 0
        ]);

        echo json_encode(['success' => true, 'id' => $id]);
    }

    public function ajax_toggle_subtask() {
        $id = $this->input->post('id');
        $this->Subtask_model->toggle($id);
        echo json_encode(['success' => true]);
    }

    public function ajax_delete_subtask() {
        $id = $this->input->post('id');
        $this->Subtask_model->delete($id);
        echo json_encode(['success' => true]);
    }

    public function ajax_edit_subtask() {
        $id    = $this->input->post('id');
        $title = $this->input->post('title');

        $this->Subtask_model->update($id, ['title' => $title]);
        echo json_encode(['success' => true]);
    }

    public function history()
{
    $todos = $this->Todo_model->get_done_todos();

    // ambil subtasks per todo
    foreach ($todos as $t) {
        $t->subtasks = $this->Todo_model->get_subtasks($t->id);
    }

    $data['todos'] = $todos;

    $this->load->view('todo/history', $data);
}

public function delete_all_history()
{
    $this->Todo_model->deleteAllHistory();
}
}
