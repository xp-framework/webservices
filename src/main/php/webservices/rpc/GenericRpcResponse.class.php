<?php namespace webservices\rpc;

/**
 * Generic RPC response.
 *
 * Instead of deriving the AbstractRpcResponse in every new
 * implementation, you can use this generic RPC response which
 * must be given a callback class (usually the RpcRouter) which
 * then can execute the actions.
 *
 * @see   xp://scriptlet.rpc.AbstractRpcRouter
 */
class GenericRpcResponse extends AbstractRpcResponse {
  public $_cb=   null;
  
  /**
   * Set callback object.
   *
   * @param   var $object
   */
  public function setCallback($object) {
    $this->_cb= $object;
  }

  /**
   * Process response. Sets the headers and response body
   * of the response.
   *
   * GenericRpcResponse delegates this to the callback object
   * (usually the Router).
   *
   * @see     scriptlet.HttpScriptletResponse#process
   */
  public function process() {
    return $this->_cb->setResponse($this);
  }
}
