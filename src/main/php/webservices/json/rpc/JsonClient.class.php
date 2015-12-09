<?php namespace webservices\json\rpc;

use lang\IllegalArgumentException;
use lang\Object;
use webservices\json\rpc\JsonRequestMessage;
use webservices\json\rpc\transport\JsonRpcHttpTransport;

/**
 * This is a Json-RPC client
 *
 * @see       http://json-rpc.org/wiki/specification
 * @see       http://json.org/
 * @purpose   Json RPC Client base class
 */
class JsonClient extends Object {
  public
    $transport  = null,
    $message    = null,
    $answer     = null;

  /**
   * Constructor.
   *
   * @param   scriptlet.rpc.transport.GenericHttpTransport transport
   */
  public function __construct($transport) {
    $this->transport= $transport;
  }
  
  /**
   * Set trace for debugging
   *
   * @param   util.log.LogCategory cat
   */
  public function setTrace($cat) {
    $this->transport->setTrace($cat);
  }
  
  /**
   * Invoke a method on a XML-RPC server
   *
   * @param   string method
   * @param   var vars
   * @return  var answer
   * @throws  lang.IllegalArgumentException
   * @throws  webservices.xmlrpc.XmlRpcFaultException
   */
  public function invoke() {
    static $serial= 1000;
    if (!$this->transport instanceof JsonRpcHttpTransport) throw new IllegalArgumentException(
      'Transport must be a webservices.json.transport.JsonRpcHttpTransport'
    );
  
    $args= func_get_args();
    
    $this->message= new JsonRequestMessage();
    $this->message->create(array_shift($args), time().(++$serial));
    $this->message->setData($args);
    
    // Send
    if (false == ($response= $this->transport->send($this->message))) return false;
    
    // Retrieve response
    if (false == ($this->answer= $this->transport->retrieve($response))) return false;
    
    $data= $this->answer->getData();
    return $data;
  }
}