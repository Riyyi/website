<?php

namespace App\Classes\Http;

class Http {

	private string $bodyContent;

	private string $bodyFormat;

	private array $options = [];

	//-------------------------------------//

	public function __construct()
	{
	}

	public function delete(string $url, $data = []): Response
	{
		return $this->send('DELETE', $url, [
			$this->bodyFormat => $data
		]);
	}

	public function get(string $url, $query = null): Response
	{
		return $this->send('GET', $url, [
			'query' => $query
		]);
	}

	public function patch(string $url, array $data = []): Response
	{
		return $this->send('PATCH', $url, [
			$this->bodyFormat => $data
		]);
	}

	public function put(string $url, array $data = []): Response
	{
		return $this->send('PUT', $url, [
			$this->bodyFormat => $data
		]);
	}

	public function post(string $url, array $data = []): Response
	{
		return $this->send('POST', $url, [
			$this->bodyFormat => $data
		]);
	}

	//-------------------------------------//

	public function accept(string $contentType): Http
	{
		return $this->withHeaders(['Accept' => $contentType]);
	}

	public function acceptJson(): Http
	{
		return $this->accept('application/json');
	}

	public function asForm(): Http
	{
		return $this->bodyFormat('form_params')
					->contentType('application/x-www-form-urlencoded');
	}

	public function asJson(): Http
	{
		return $this->bodyFormat('json')
					->contentType('application/json');
	}

	public function bodyFormat(string $format): Http
	{
		$this->bodyFormat = $format;
		return $this;
	}

	public function contentType(string $type): Http
	{
		return $this->withHeaders(['Content-Type' => $type]);
	}

	public function withBody(string $content, string $contentType): Http
	{
		$this->bodyFormat('body');

		$this->bodyContent = $content;

		$this->contentType($contentType);

		return $this;
	}

	public function withHeaders(array $headers): Http
	{
		$this->options = array_merge_recursive($this->options, [
			'headers' => $headers,
		]);

		return $this;
	}

	public function withToken(string $token): Http
	{
		$this->withHeaders(['Authorization' => 'Bearer ' . trim($token)]);
		return $this;
	}

	//-------------------------------------//

	public function send(string $method, string $url, array $options = []): Response
	{
		// Format headers
		$headers = [];
		if (_exists($this->options, 'headers')) {
			foreach ($this->options['headers'] as $key => $value) {
				$headers[] = "$key: $value";
			}
		}

		// Fill body content
		switch ($this->bodyFormat) {
			case 'body':
				break;
			case 'json':
				if (_exists($options, 'json')) {
					$this->bodyContent = json_encode($options['json']);
				}
				break;
			case 'form_params':
				if (_exists($options, 'form_params')) {
					$this->bodyContent = http_build_query($options['form_params']);
				}
				break;
		}

		// Send HTTP request
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL,             $url);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST,   $method);
		curl_setopt($curl, CURLOPT_HTTPHEADER,      $headers);
		curl_setopt($curl, CURLOPT_POSTFIELDS,      $this->bodyContent);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER,  1);
		$response = curl_exec($curl);
		curl_close($curl);

		// On failed requests
		if (!$response) {
			$response = '';
		}

		return new Response($response);
	}

}
