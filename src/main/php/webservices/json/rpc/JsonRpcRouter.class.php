<?php namespace webservices\json\rpc;

use webservices\rpc\AbstractRpcRouter;

/**
 * JSON RPC Router class. You can use this class to implement
 * a JSON webservice.
 *
 * @see      xp://webservices.json.rpc.JsonClient
 * @purpose  JSON-RPC-Service
 */
class JsonRpcRouter extends AbstractRpcRouter {

  /**
   * Create a request object.
   *
   * @return  scriptlet.rpc.AbstractRpcRequest
   */
  protected function _request() {
    return new JsonRpcRequest();
  }

  /**
   * Create a response object.
   *
   * @return  scriptlet.rpc.AbstractRpcResponse
   */
  protected function _response() {
    return new JsonRpcResponse();
  }
  
  /**
   * Create a message object.
   *
   * @return  scriptlet.rpc.AbstractRpcMessage
   */
  protected function _message() {
    return new JsonResponseMessage();
  }
}
