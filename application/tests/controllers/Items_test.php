<?php

class Items_test extends UnitTestCase {
	protected $token;
	protected $id;

	public function setUp():void {
		$this->model = $this->newModel('Item_model');
		$this->checklist_model = $this->newModel('Checklist_model');
		$this->id = $this->model->getAll()[0]->id;
		$this->checklist_id = $this->checklist_model->getAll()[0]->id;
		$this->token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MSwibmFtZSI6IlVTRVJOQU1FIn0.cixGJ_NzxjWonaNnpJrL0mJvOuWePhN4ayO367HXP_E";
	}

	public function test_index() {
		$output = $this->request('GET', 'items?token='.$this->token);

    $this->assertArrayHasKey('data', json_decode($output, true));
	}

	public function test_index_401_unauthorized() {
		$output = $this->request('GET', 'items');
    $this->assertResponseCode(401);
	}

	public function test_create() {
		$output = $this->request(
			'POST', 
			'items/create?token='.$this->token,
			'{"name":"test_item'.rand(100, 1000).'", "checklist_id": '.$this->checklist_id.'}'
		);

    $this->assertEquals(['message' => 'success'], json_decode($output, true)) ;
	}

	public function test_create_500() {
		$output = $this->request('POST', 'items/create?token='.$this->token);

    $this->assertResponseCode(500);
	}

	public function test_update() {
		$output = $this->request(
			'POST', 
			'items/update/'.$this->id.'?token='.$this->token,
			'{"name":"test_item'.rand(100, 1000).'", "checklist_id": '.$this->checklist_id.'}'
		);

    $this->assertEquals(['message' => 'success'], json_decode($output, true)) ;
	}

	public function test_update_404() {
		$output = $this->request(
			'POST', 
			'items/update/0?token='.$this->token,
			'{"name":"test_item'.rand(100, 1000).'", "checklist_id": '.$this->checklist_id.'}'
		);

    $this->assertResponseCode(404);
	}

	public function test_delete() {
		$output = $this->request('DELETE', 'items/delete/'.$this->id.'?token='.$this->token);

    $this->assertEquals(['message' => 'success'], json_decode($output, true)) ;
	}
}