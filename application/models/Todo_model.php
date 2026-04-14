<?php
class Todo_model extends CI_Model {

    protected $table = 'todos';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_all() {
        return $this->db->order_by('created_at','DESC')->get($this->table)->result();
    }

    public function get($id) {
        return $this->db->where('id', $id)->get($this->table)->row();
    }

    public function insert($data) {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data) {
        return $this->db->where('id', $id)->update($this->table, $data);
    }

    public function delete($id) {
        return $this->db->where('id', $id)->delete($this->table);
    }

    public function insertSubtask($data)
{
    return $this->db->insert('subtasks', $data);
}

public function getHistory()
{
    return $this->db
        ->where('is_done', 1)
        ->order_by('created_at', 'DESC')
        ->get('todos')
        ->result();
}

public function get_done_todos()
{
    return $this->db->where('is_done', 1)
                    ->order_by('created_at', 'DESC')
                    ->get('todos')
                    ->result();
}

public function get_subtasks($todo_id)
{
    return $this->db->where('todo_id', $todo_id)
                    ->get('subtasks')
                    ->result();
}

public function deleteAllHistory()
{
    return $this->db->where('is_done', 1)->delete('todos');
}
}
