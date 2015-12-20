<?php namespace webservices\json\rpc\transport;

use lang\IllegalAccessException;
use lang\IllegalStateException;
use lang\IllegalArgumentException;
use peer\http\HttpConnection;
use peer\http\HttpConstants;
use peer\http\HttpRequest;
use peer\http\RequestData;
use webservices\rpc\RpcFaultException;
use webservices\rpc\transport\AbstractRpcTransport;
use webservices\json\rpc\JsonMessage;
use webservices\json\rpc\JsonResponseMessage;

/**
 * Transport for JSON RPC requests over HTTP.
 *
 * @purpose  HTTP Transport for RPC clients
 */
class JsonRpcHttpTransport extends AbstractRpcTransport {
  public
    $_conn    = null,
    $_headers = [];
  
  /**
   * Constructor.
   *
   * @param   string url
   * @param   array headers
   */
  public function __construct($url, $headers= []) {
    $this->_conn= new HttpConnection($url);
    $this->_headers= $headers;
  }
  
  /**
   * Create a string representation
   *
   * @return  string
   */
  public function toString() {
    return sprintf('%s { %s }', $this->getClassName(), $this->_conn->request->url->_info['url']);
  }
  
  /**
   * Send RPC message
   *
   * @param   scriptlet.rpc.AbstractRpcMessage message
   * @return  scriptlet.HttpScriptletResponse
   */
  public function send($message) {
    if (!$message instanceof JsonMessage) {
      throw new IllegalArgumentException('Expected message to be of type JsonMessage.');
    }

    with ($request= $this->_conn->create(new HttpRequest())); {
      $request->setMethod(HttpConstants::POST);
      $request->setParameters(new RequestData($message->serializeData()));
      $request->setHeader('Content-Type', $message->getContentType().'; charset='.$message->getEncoding());
      $request->setHeader('User-Agent', 'XP Framework Client (http://xp-framework.net)');

      // Add custom headers
      $request->addHeaders($this->_headers);

      $this->cat && $this->cat->debug('>>>', $request->getRequestString());
      return $this->_conn->send($request);
    }
  }
  
  /**
   * Retrieve a RPC message.
   *
   * @param   scriptlet.HttpScriptletResponse response
   * @return  scriptlet.rpc.AbstractRpcMessage
   */
  public function retrieve($response) {
    $this->cat && $this->cat->debug('<<<', $response->toString());
    
    $code= $response->getStatusCode();
    switch ($code) {
      case HttpConstants::STATUS_OK:
      case HttpConstants::STATUS_INTERNAL_SERVER_ERROR:
        $xml= '';
        while ($buf= $response->readData()) $xml.= $buf;

        $this->cat && $this->cat->debug('<<<', $xml);
        $answer= JsonResponseMessage::fromString($xml);
        if ($answer) {

          // Check encoding
          if (null !== ($content_type= $response->getHeader('Content-Type'))) {
            @list($type, $charset)= explode('; charset=', $content_type);
            if (!empty($charset)) $answer->setEncoding($charset);
          }
        }

        // Fault?
        if (null !== ($fault= $answer->getFault())) {
          throw new RpcFaultException($fault);
        }
        
        return $answer;
      
      case HttpConstants::STATUS_AUTHORIZATION_REQUIRED:
        throw new IllegalAccessException(
          'Authorization required: '.$response->getHeader('WWW-Authenticate')
        );
      
      default:
        throw new IllegalStateException(
          'Unexpected return code: '.$response->getStatusCode()
        );
    }
  }    
}