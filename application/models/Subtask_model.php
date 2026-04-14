<?php
class Subtask_model extends CI_Model {

    public function get_by_todo($todo_id) {
        return $this->db->where('todo_id',$todo_id)
                        ->order_by('id','ASC')
                        ->get('subtasks')
                        ->result();
    }

    public function insert($data) {
        $this->db->insert('subtasks',$data);
        return $this->db->insert_id();
    }

    public function toggle($id) {
        $s = $this->db->where('id',$id)->get('subtasks')->row();
        $new = $s->is_done ? 0 : 1;

        return $this->db->where('id',$id)->update('subtasks',[
            'is_done'=>$new
        ]);
    }

    public function update($id,$data) {
        return $this->db->where('id',$id)->update('subtasks',$data);
    }

    public function delete($id) {
        return $this->db->where('id',$id)->delete('subtasks');
    }

    public function delete_by_todo($todo_id) {
        return $this->db->where('todo_id',$todo_id)->delete('subtasks');
    }
}
