<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use \Firebase\JWT\JWT;
use Firebase\JWT\Key;

class BaseController extends CI_Controller {
	public function __construct() {
		parent::__construct();
	}

	public function response($results = array(), $status = 200)
	{
		return $this->output
			->set_status_header($status)
			->set_content_type('application/json')
			->set_output(json_encode($results));
	}

	public function checkToken() {
		try {
			$token = isset($_GET['token']) ? $_GET['token'] : null;

			if (empty($token)) {
				$this->setUnauthorized();
				echo json_encode(['message' => 'Unauthorized']);exit;
			}else {
				$this->decodeToken();
			}
		} catch(Exception $e) {
			$this->setUnauthorized();
			echo json_encode(['message' => $e->getMessage()]);exit;
		}
	}

	public function decodeToken() {
		$token = isset($_GET['token']) ? $_GET['token'] : null;
		$decoded = (array) JWT::decode($token, new Key($_ENV['JWT_KEY'], 'HS256'));
		if (!isset($decoded['id']) || empty($decoded['id'])) {
			$this->setUnauthorized();
			echo json_encode(['message' => 'Invalid Key']);exit;
		}
		return $decoded;
	}

	public function requestStream() {
		$stream = $this->security->xss_clean( $this->input->raw_input_stream );
		return json_decode(trim($stream), true);
	}

	function setUnauthorized() {
		$this->output
			->set_status_header(401)
			->set_content_type('application/json');
	}
}
