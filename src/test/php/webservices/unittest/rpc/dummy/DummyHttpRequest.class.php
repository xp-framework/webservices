<?php namespace webservices\unittest\rpc\dummy;

use peer\http\HttpRequest;
use peer\http\HttpResponse;

/**
 * Dummy HTTP request
 */
class DummyHttpRequest extends HttpRequest {
  public $_response= '';
    
  /**
   * Constructor
   *
   * @param   string $data
   */
  public function setResponse($data) {
    $this->_response= $data;
  }    
  
  /**
   * Send request
   *
   * @return  &peer.http.HttpResponse response object
   */
  public function send($timeout= 60) {
    return new HttpResponse(new DummySocket($this->_response));
  }
}
