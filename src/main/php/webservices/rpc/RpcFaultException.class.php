<?php namespace webservices\rpc;

/**
 * Indicates an RPC fault occurred.
 */
class RpcFaultException extends \lang\XPException {
  public $fault= null;
  
  /**
   * Constructor
   *
   * @param   scriptlet.rpc.RpcFault $fault
   */
  public function __construct($fault) {
    parent::__construct($fault->faultString);
    $this->fault= $fault;
  }

  /**
   * Get Fault
   *
   * @return  scriptlet.rpc.RpcFault
   */
  public function getFault() {
    return $this->fault;
  }

  /**
   * Return compound message of this exception.
   *
   * @return  string
   */
  public function compoundMessage() {
    return sprintf(
      "Exception %s (%s) {\n".
      "  fault.faultcode   = %s\n".
      "  fault.faultstring = '%s'\n".
      "}\n",
      nameof($this),
      $this->message,
      $this->fault->faultCode,
      $this->fault->faultString
    );
  }
}
