<?php
namespace QvaPay;

require __DIR__ . '/../vendor/autoload.php';
use Curl\Curl;
class Api
{
	public $curl;
	protected $base_url = 'https://qvapay.com/api/v1/';

	public function __construct()
	{
		$this->curl = new Curl();
	}

	/**
	 * @param string $endpoint
	 * @param array  $data
	 */
	public function get($endpoint = 'info', $data = array()){
		$this->curl->get($this->base_url.$endpoint,$data);
	}

	/**
	 * @param string $endpoint
	 * @param array  $data
	 */
	public function post($endpoint = 'info', $data = array()){
		$this->curl->post($this->base_url.$endpoint,$data);
	}

	public function response(){
		return $this->curl->response;
	}

	public function error(){
		return $this->curl->error;
	}

	public function errorCode(){
		return $this->curl->errorCode;
	}

	public function errorMessage(){
		return $this->curl->errorMessage;
	}
}