<?php

namespace App\Classes\Http;

class Response {

	private string $response;

	public function __construct(string $response)
	{
		$this->response = $response;
	}

	//-------------------------------------//

	public function body(): string
	{
		return $this->response;
	}

}
