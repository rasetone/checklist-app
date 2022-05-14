<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH."controllers/BaseController.php");

class Items extends BaseController {
	protected $user_id;
	protected $name;
	protected $checklist_id;

	public function __construct(){
		parent::__construct();
		$this->load->model('Item_model', 'im');
		$this->load->model('Checklist_model', 'cm');
		$this->checkToken();
		$decoded_token = $this->decodeToken();
		$this->user_id = $decoded_token['id'];
	}

	public function index() {
		$perpage = isset($_GET['perpage']) ? $_GET['perpage'] : 10;
		$page = isset($_GET['page']) ? $_GET['page'] : 1;
		
		$filters = array();
		$checklist_id = isset($_GET['checklist_id']) ? $_GET['checklist_id'] : null;
		if (!empty($checklist_id)) $filters['checklist_id'] = $checklist_id;

		$results = $this->im->getAll($page, $perpage, $filters);	
		$this->response(['data' => $results]);
	}

	public function create() {
		$this->getRequest();
		$name = $this->name;
		$checklist_id = $this->checklist_id;

		if (empty($name)) {
			$this->response(['message' => 'Name is required'], 500);
		} else if (empty($checklist_id)) {
			$this->response(['message' => 'Checklist Relation is required'], 500);
		} else if (is_null($this->cm->find($this->checklist_id))) {
			$this->response(['message' => 'Checklist Relation is not found'], 500);
		} else {
			$input['name'] = $name;
			$input['checklist_id'] = $checklist_id;
			$input['created_by'] = $this->user_id;

			if ($this->im->insert($input) > 0) {
				$this->response(['message' => 'success'], 201);
			} else {
				$this->response(['message' => 'failed'], 500);
			}
		} 
	}

	public function update($id) {
		$row = $this->im->find(addslashes($id));

		if (!is_null($row)) {
			$this->getRequest();
			$name = $this->name;
			$checklist_id = $this->checklist_id;

			if (empty($name)) {
				$this->response(['message' => 'Name is required'], 500);
			} else if (empty($checklist_id)) {
				$this->response(['message' => 'Checklist Relation is required'], 500);
			} else if (is_null($this->cm->find($this->checklist_id))) {
				$this->response(['message' => 'Checklist Relation is not found'], 500);
			} else {
				$input['name'] = $name;
				$input['checklist_id'] = $checklist_id;
				$input['updated_by'] = $this->user_id;

				if ($this->im->update($id, $input) > 0) {
					$this->response(['message' => 'success'], 201);
				} else {
					$this->response(['message' => 'failed'], 500);
				}
			} 
		} else {
			$this->response(['message' => 'Not found'], 404);

		}
	}

	public function delete($id) {
		$row = $this->im->find(addslashes($id));

		if (!is_null($row)) {
			$input['deleted_at'] = Date('Y-m-d H:i:s');
			$input['deleted_by'] = $this->user_id;

			if ($this->im->update($id, $input)) {
				$this->response(['message' => 'success']);
			} else {
				$this->response(['message' => 'failed'], 500);
			}
		} else {
			$this->response(['message' => 'Not Found'], 404);
		}
	}

	function getRequest() {
		$request = $this->requestStream();
		$this->name = isset($request['name']) ? $request['name'] : null;
		$this->checklist_id = isset($request['checklist_id']) ? $request['checklist_id'] : null;
	}
}
