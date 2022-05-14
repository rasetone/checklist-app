<?php
class User_model extends CI_Model {
  protected $table;
  
  public function __construct()
  {
    $this->table = 'users';
    $this->load->database();
  }

  public function getUser($username, $password) {
    $hashed = md5($password);
  	$this->db->where("LOWER(username) = '".$username."' AND password = '".$hashed."'");
  	$this->db->select('id, username');
  	$query = $this->db->get($this->table);
  	return $query->row();
  }
}