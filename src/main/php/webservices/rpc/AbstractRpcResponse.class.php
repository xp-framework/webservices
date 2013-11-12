<?php namespace webservices\rpc;

use scriptlet\HttpScriptletResponse;
use util\log\Traceable;

/**
 * RPC response object
 *
 * @see    xp://scriptlet.rpc.AbstractRpcRouter
 */
class AbstractRpcResponse extends HttpScriptletResponse implements Traceable {
  public $message = null;
  public $cat     = null;
  
  /**
   * Constructor
   */
  public function __construct() {
    $this->setHeader('Server', 'Abstract RPC 1.0 / PHP'.phpversion().' / XP Framework');
  }
  
  /**
   * Sets message object
   *
   * @param   scriptlet.rpc.AbstractRpcMessage $msg
   */
  public function setMessage($msg) {
    $this->message= $msg;
  }
  
  /**
   * Set trace for debugging
   *
   * @param   util.log.LogCategory $cat
   */
  public function setTrace($cat) {
    $this->cat= $cat;
  }
} 
