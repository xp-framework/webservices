<?php namespace webservices\soap\rpc;
 
use webservices\rpc\AbstractRpcResponse;
use peer\http\HttpConstants;


/**
 * Wraps SOAP response
 *
 * @see scriptlet.HttpScriptletResponse  
 */
class SoapRpcResponse extends AbstractRpcResponse {
  
  /**
   * Make sure a fault is passed as "500 Internal Server Error"
   *
   * @see     scriptlet.HttpScriptletResponse#process
   */
  public function process() {
    if (!$this->message) return;

    $this->setHeader('Content-type', 'text/xml');      
    if (null !== $this->message->getFault()) {
      $this->setStatus(HttpConstants::STATUS_INTERNAL_SERVER_ERROR);
    }
    
    $this->content= $this->message->serializeData();
  }
}
