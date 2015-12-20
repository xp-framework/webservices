<?php namespace webservices\xmlrpc;

/**
 * XmlRpc response message. 
 *
 * @ext      xml
 * @see      xp://webservices.xmlrpc.XmlRpcRequestMessage
 * @purpose  Wrap XML-RPC Response
 */
class XmlRpcResponseMessage extends XmlRpcMessage {

  /**
   * Create a response message for the given request
   *
   * @param   webservices.xmlrpc.XmlRpcRequestMessage reqmsg
   */
  public function create($reqmsg= null) {
    $this->tree= new \xml\Tree(XMLRPC_RESPONSE);
  }
  
  /**
   * Construct a XML-RPC response message from a string
   *
   * <code>
   *   $msg= XmlRpcResponseMessage::fromString('<methodCall>...</methodCall>');
   * </code>
   *
   * @param   string string
   * @return  webservices.xmlrpc.XmlRpcResponse Message
   */
  public static function fromString($string) {
    $msg= new self();
    $msg->tree= \xml\Tree::fromString($string);

    if (!$msg->tree->root()->hasChildren()) {
      throw new \lang\FormatException('Response is not well formed'); 
    }

    // Set class and method members from XML structure
    $target= $msg->tree->root()->nodeAt(0)->getContent();
    @list($msg->class, $msg->method)= explode('.', $target);

    return $msg;
  }
  
  /**
   * Set the data for the message.
   *
   * @param   var arr
   */
  public function setData($value) {
    $encoder= new XmlRpcEncoder();

    $params= $this->tree->root()->addChild(new \xml\Node('params'));
    $param= $params->addChild(new \xml\Node('param'));
    $param->addChild($encoder->encode($value));
  }
  
  /**
   * Return the data from the message.
   *
   * @return  var
   */
  public function getData() {
    $ret= [];
    
    if (
      !is('xml.Node', $this->tree->root()->nodeAt(0)->nodeAt(0)->nodeAt(0)) ||
      'value' != $this->tree->root()->nodeAt(0)->nodeAt(0)->nodeAt(0)->getName()
    ) {
      throw new \lang\IllegalStateException('No node "params" found.');
    }

    // Process params node
    $decoder= new XmlRpcDecoder();
    
    // Access node /methodResponse/params/param/value node
    return $decoder->decode($this->tree->root()->nodeAt(0)->nodeAt(0)->nodeAt(0));
  }
}
