<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH."controllers/BaseController.php");

use Predis\Client;

class Checklist extends BaseController {
	protected $user_id;

	public function __construct(){
		parent::__construct();
		$this->load->model('Checklist_model', 'cm');
		$this->checkToken();
		$decoded_token = $this->decodeToken();
		$this->user_id = $decoded_token['id'];
	}

	public function index() {
		#sample caching data with redis
		$client = new Predis\Client([
		 	'scheme' => 'tcp',
			'host'   => '127.0.0.1',
			'port'   => 6379,
		]);

		$key = 'all_checklist';

		$cached = $client->get($key);
		
		if($cached != null){
			$results = $cached;
		} else{
			$results = serialize($this->cm->getAll());
			$client->set($key, $results);
			$client->expire($key, 300);     
		}
		
		$this->response(['data' => unserialize($results)]);
	}

	public function create() {
		$name = $this->getName();

		if (empty($name)) {
			$this->response(['message' => 'Name is required'], 500);
		} else {
			$input['name'] = $name;
			$input['created_by'] = $this->user_id;

			if ($this->cm->insert($input) > 0) {
				$this->response(['message' => 'success'], 201);
			} else {
				$this->response(['message' => 'failed'], 500);
			}
		} 
	}

	public function update($id) {
		$row = $this->cm->find(addslashes($id));

		if (!is_null($row)) {
			$name = $this->getName();
			
			if (empty($name)) {
				$this->response(['message' => 'Name is required'], 500);
			} else {
				$input['name'] = $name;
				$input['updated_by'] = $this->user_id;

				if ($this->cm->update($id, $input)) {
					$this->response(['message' => 'success']);
				} else {
					$this->response(['message' => 'failed'], 500);
				}
			}

		} else {
			$this->response(['message' => 'Not Found'], 404);
		}
	}

	public function delete($id) {
		$row = $this->cm->find(addslashes($id));

		if (!is_null($row)) {
			$input['deleted_at'] = Date('Y-m-d H:i:s');
			$input['deleted_by'] = $this->user_id;

			if ($this->cm->update($id, $input)) {
				$this->response(['message' => 'success']);
			} else {
				$this->response(['message' => 'failed'], 500);
			}
		} else {
			$this->response(['message' => 'Not Found'], 404);
		}
	}

	function getName() {
		$request = $this->requestStream();
		$name = isset($request['name']) ? $request['name'] : null;
		return $name;
	}
}
