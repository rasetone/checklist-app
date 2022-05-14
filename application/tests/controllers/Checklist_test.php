<?php

class Checklist_test extends UnitTestCase {
	protected $token;
	protected $id;

	public function setUp():void {
		$this->model = $this->newModel('Checklist_model');
		$this->id = $this->model->getAll()[0]->id;
		$this->token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MSwibmFtZSI6IlVTRVJOQU1FIn0.cixGJ_NzxjWonaNnpJrL0mJvOuWePhN4ayO367HXP_E";
	}

	public function test_index() {
		$output = $this->request('GET', 'checklist?token='.$this->token);

    $this->assertArrayHasKey('data', json_decode($output, true));
	}

	public function test_index_401_unauthorized() {
		$output = $this->request('GET', 'checklist');
    $this->assertResponseCode(401);
	}

	public function test_create() {
		$output = $this->request(
			'POST', 
			'checklist/create?token='.$this->token,
			'{"name":"test_checklist'.rand(100, 1000).'"}'
		);

    $this->assertEquals(['message' => 'success'], json_decode($output, true)) ;
	}

	public function test_create_500() {
		$output = $this->request('POST', 'checklist/create?token='.$this->token);

    $this->assertResponseCode(500);
	}

	public function test_update() {
		$output = $this->request(
			'POST', 
			'checklist/update/'.$this->id.'?token='.$this->token,
			'{"name":"test_checklist'.rand(100, 1000).'"}'
		);

    $this->assertEquals(['message' => 'success'], json_decode($output, true)) ;
	}

	public function test_update_404() {
		$output = $this->request(
			'POST', 
			'checklist/update/0?token='.$this->token,
			'{"name":"test_checklist'.rand(100, 1000).'"}'
		);

    $this->assertResponseCode(404);
	}

	public function test_delete() {
		$output = $this->request('DELETE', 'checklist/delete/'.$this->id.'?token='.$this->token);

    $this->assertEquals(['message' => 'success'], json_decode($output, true)) ;
	}
}