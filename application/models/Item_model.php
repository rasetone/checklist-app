<?php
class Item_model extends CI_Model {
  protected $table;
  
  public function __construct()
  {
    $this->table = 't_items';
    $this->load->database();
  }

  public function find($id) {
    $this->db->where('deleted_at IS NULL AND id = '.$id);
    $query = $this->db->get($this->table);
    return $query->row();
  }

  public function getAll($page = 1, $perpage = 10, $filter = array()) {
    $where = 't_items.deleted_at IS NULL';
    if (isset($filter['checklist_id'])) $where .= " AND t_items.checklist_id = ".$filter['checklist_id'];

  	$this->db->where($where);
  	$this->db->select('t_items.id, t_items.name, t_items.checklist_id, t_items.status, m_checklist.name as checklist_name, t_items.created_at, t_items.updated_at');
    $this->db->join('m_checklist', 'm_checklist.id = t_items.checklist_id');
    
  	$start = ($page - 1) * $perpage;
  	$this->db->limit($perpage, $start);
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