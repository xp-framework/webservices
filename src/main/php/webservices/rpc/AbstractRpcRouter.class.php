<?php namespace webservices\rpc;

use scriptlet\HttpScriptlet;
use util\ServiceException;
use util\log\Traceable;
use util\log\Logger;
use xml\Node;

/**
 * Provide RPC services
 */
abstract class AbstractRpcRouter extends HttpScriptlet implements Traceable {
  public
    $package      = null,
    $cat          = null;
  
  /**
   * Constructor
   *
   * @param   string $package
   */
  public function __construct($package) {
    $this->package= $package;
  }
  
  /**
   * Set trace for debugging
   *
   * @param   util.log.LogCategory $cat
   */
  public function setTrace($cat) {
    $this->cat= $cat;
  }
  
  /**
   * Create a request object.
   *
   * @return  scriptlet.rpc.AbstractRpcRequest
   */
  protected function _request() {}

  /**
   * Create a response object.
   *
   * @return  scriptlet.rpc.AbstractRpcResponse
   */
  protected function _response() {}

  /**
   * Create a message object.
   *
   * @return  scriptlet.rpc.AbstractRpcMessage
   */
  protected abstract function _message();

  /**
   * Handle GET requests. XML-RPC requests are only sent via HTTP POST,
   * so GET isn't supported.
   *
   * @param   scriptlet.rpc.AbstractRpcRequest $request
   * @param   scriptlet.rpc.AbstractRpcResponse $response
   */
  public function doGet($request, $response) {
    throw new \lang\IllegalAccessException('GET is not supported');
  }

  /**
   * Formats stack trace to be used in SOAP fault messages. Makes sure
   * the stack trace elements' string representation is XML-safe (unsafe 
   * characters are replaced with the character � (ASCII #191)).
   *
   * @param   lang.StackTraceElement[] $elements
   * @return  string[]
   */
  public function formatStackTrace($elements) {
    $stacktrace= [];
    $replace= str_repeat('�', strlen(Node::XML_ILLEGAL_CHARS));
    for ($i= 0, $s= sizeof($elements); $i < $s; $i++) {
      $stacktrace[]= strtr($elements[$i]->toString(), Node::XML_ILLEGAL_CHARS, $replace);
    }
    return $stacktrace;
  }

  /**
   * Handle POST requests. The POST data carries the XML-RPC
   * request.
   *
   * @param   scriptlet.rpc.AbstractRpcRequest $request
   * @param   scriptlet.rpc.AbstractRpcResponse $response
   */
  public function doPost($request, $response) {
    $this->cat && $response->setTrace($this->cat);
    $this->cat && $request->setTrace($this->cat);
    
    $hasFault= false;
    $answer= $this->_message();

    try {

      // Get message
      $msg= $request->getMessage();

      // Create answer
      $answer->create($msg);

      // Call handler
      $return= $this->callReflectHandler($msg);

    } catch (\lang\reflect\TargetInvocationException $e) {
      $hasFault= true;
      
      // Log exception
      Logger::getInstance()->getCategory(nameof($this))->error(
        nameof($this),
        '~ invocation returned exception',
        $e
      );

      $cause= $e->getCause();
      if ($cause instanceof ServiceException) {
        
        // Server methods may throw a ServiceException to have more
        // convenient control over the faultcode which is returned to the client.
        $answer->setFault(
          $cause->getFaultcode(),
          $cause->getMessage(),
          $request->getEnvValue('SERVER_NAME').':'.$request->getEnvValue('SERVER_PORT'),
          $this->formatStackTrace($cause->getStackTrace())
        );
      } else {
        $answer->setFault(
          \peer\http\HttpConstants::STATUS_INTERNAL_SERVER_ERROR,
          $cause->getMessage(),
          $request->getEnvValue('SERVER_NAME').':'.$request->getEnvValue('SERVER_PORT'),
          $this->formatStackTrace($cause->getStackTrace())
        );
      }
    } catch (\lang\XPException $e) {
      // Log exception
      Logger::getInstance()->getCategory(nameof($this))->error(
        nameof($this),
        '~ invocation returned exception',
        $e
      );

      $answer->setFault(
        \peer\http\HttpConstants::STATUS_INTERNAL_SERVER_ERROR,
        $e->getMessage(),
        $request->getEnvValue('SERVER_NAME').':'.$request->getEnvValue('SERVER_PORT'),
        $this->formatStackTrace($e->getStackTrace())
      );
      
      $hasFault= true;
    }
    
    $hasFault || $answer->setData($return);
    
    // Set message
    $response->setHeader('Content-type', $answer->getContentType().'; charset='.$answer->getEncoding());
    $response->setMessage($answer);
  }
  
  /**
   * Calls the handler that the action reflects to
   *
   * @param   scriptlet.rpc.AbstractRpcMessage message object (from request)
   * @return  var result of method call
   * @throws  lang.IllegalArgumentException if there is no such method
   * @throws  lang.IllegalAccessException for non-public methods
   */
  public function callReflectHandler($msg) {
  
    // Check on valid params
    if (0 == strlen($msg->getMethod())) {
      throw new \lang\IllegalArgumentException('No method name passed.');
    }

    // Create message from request data
    $class= \lang\XPClass::forName($this->package.'.'.ucfirst($msg->getHandlerClass()).'Handler');

    // Check if method can be handled
    if (!$class->hasMethod($msg->getMethod())) {
      throw new \lang\IllegalArgumentException(
        $class->getName().' cannot handle method '.$msg->getMethod()
      );
    }

    with ($method= $class->getMethod($msg->getMethod())); {

      // Check if this method is a webmethod
      if (!$method->hasAnnotation('webmethod')) {
        throw new \lang\IllegalAccessException('Cannot access non-web method '.$msg->getMethod());
      }
      
      // Create instance and invoke method
      $inst= $class->newInstance();
      if ($this->cat && is('util.log.Traceable', $inst)) $inst->setTrace($this->cat);
      return $method->invoke($inst, $msg->getData());
    }
  }
} 
