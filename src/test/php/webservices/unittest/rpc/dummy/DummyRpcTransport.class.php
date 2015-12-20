<?php namespace webservices\unittest\rpc\dummy;

/**
 * Dummy Transport
 */
class DummyRpcTransport extends \webservices\xmlrpc\transport\XmlRpcHttpTransport {

  /**
   * Constructor
   *
   * @param   string $url
   * @param   [:string] $headers default array
   */
  public function __construct($url, $headers= []) {
    $this->_conn= new DummyHttpConnection($url);
    $this->_headers= $headers;
  }
  
  /**
   * Retrieve connection
   *
   * @return  peer.http.HttpConnection
   */
  public function getConnection() {
    return $this->_conn;
  }
}
