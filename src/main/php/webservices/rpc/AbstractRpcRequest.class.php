<?php namespace webservices\rpc;

use scriptlet\HttpScriptletRequest;
use util\log\Traceable;

/**
 * RPC request
 *
 * @see   xp://scriptlet.rpc.AbstractRpcRouter
 */
abstract class AbstractRpcRequest extends HttpScriptletRequest implements Traceable {
  public $cat= null;
  
  /**
   * Set trace for debugging
   *
   * @param   util.log.LogCategory $cat
   */
  public function setTrace($cat) {
    $this->cat= $cat;
  }

  /**
   * Create message from request
   * Override this. You must set the 'class' and 'method' members of
   * the respective RpcMessage class.
   *
   * Be sure to set the encoding appropriately
   *
   * @return  scriptlet.rpc.AbstractRpcMessage
   */
  protected abstract function getMessage();
  
  /**
   * Determine encoding.
   *
   * @return  string
   */
  public function getEncoding() {

    // Figure out encoding if given
    $type= $this->getHeader('Content-type');
    if (false !== ($pos= strpos($type, 'charset='))) {
      return substr($type, $pos+ 8);
    }
    
    return \xp::ENCODING;
  }
} 
