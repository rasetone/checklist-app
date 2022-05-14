<?php
class Checklist_model extends CI_Model {
  protected $table;

  public function __construct()
  {
    $this->table = 'm_checklist';
    $this->load->database();
  }

  public function find($id) {
    $this->db->where('deleted_at IS NULL AND id = '.$id);
  	$query = $this->db->get($this->table);
  	return $query->row();
  }

  public function getAll() {
  	$this->db->where('deleted_at IS NULL');
  	$this->db->select('id, name, created_at, updated_at');
  	$this->db->order_by('name');
  	$query = $this->db->get($this->table);
  	return $query->result();
  }

  public function insert($input) {
  	$this->db->insert($this->table, $input);
  	$insert_id = $this->db->insert_id();

	  return $insert_id;
  }

  public function update($id, $input) {
  	return $this->db->update($this->table, $input, array('id' => $id));
  }
}