<?php namespace webservices\unittest\rpc\dummy;

/**
 * Dummy HTTP connection for unittesting
 */
class DummyHttpConnection extends \peer\http\HttpConnection {

  /**
   * Create request
   *
   * @param   peer.URL url
   */
  protected function _createRequest($url) {
    $this->request= new DummyHttpRequest($url);
  }
}
