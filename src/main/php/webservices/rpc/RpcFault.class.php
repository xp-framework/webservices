<?php namespace webservices\rpc;

/**
 * Represent an RPC Fault
 */
class RpcFault extends \lang\Object {
  public
    $faultCode=     0,
    $faultString=   '';

  /**
   * Constructor
   *
   * @param   int $code
   * @param   string $string
   */
  public function __construct($code, $string) {
    $this->faultCode= $code;
    $this->faultString= $string;
  }

  /**
   * Set FaultCode
   *
   * @param   int $faultCode
   */
  public function setFaultCode($faultCode) {
    $this->faultCode= $faultCode;
  }

  /**
   * Get FaultCode
   *
   * @return  int
   */
  public function getFaultCode() {
    return $this->faultCode;
  }

  /**
   * Set FaultString
   *
   * @param   string $faultString
   */
  public function setFaultString($faultString) {
    $this->faultString= $faultString;
  }

  /**
   * Get FaultString
   *
   * @return  string
   */
  public function getFaultString() {
    return $this->faultString;
  }
}
