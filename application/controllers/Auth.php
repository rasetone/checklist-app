<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH."controllers/BaseController.php");

use \Firebase\JWT\JWT;

class Auth extends BaseController {

	public function __construct(){
		parent::__construct();
		$this->load->model('User_model', 'um');
	}

	public function login(){
		$request = $this->requestStream();
		$username = isset($request['username']) ? $request['username'] : null;
		$username = addslashes(strtolower($request['username']));
		$password = isset($request['password']) ? $request['password'] : null;

		$user = $this->um->getUser($username, $password);

		if (is_null($user)) {
			$this->response(['message' => 'Invalid User'], 401);
		} else {
			$key = $_SERVER['JWT_KEY'];
			$token = array(
				"id" => $user->id,
				"name" => $user->username,
			);

			$jwt = JWT::encode($token, $key, 'HS256');

			$this->response(['data' => ['token' => $jwt]]);
		}

	}
}