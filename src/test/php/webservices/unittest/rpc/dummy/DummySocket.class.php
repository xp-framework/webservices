<?php namespace webservices\unittest\rpc\dummy;

/**
 * Dummy socket
 */
class DummySocket extends \peer\Socket {
  public $isConnected  = false;

  /**
   * Constructor
   *
   * @param   string string
   */
  public function __construct($string) {
    $this->_data= explode("\n", $string);
  }
  
  /**
   * Returns whether this socket is connected
   *
   * @return  bool
   */
  public function isConnected() {
    return $this->isConnected;
  }    
  
  /**
   * Connect
   *
   * @param   float timeout default 2.0
   * @return  bool
   */
  public function connect($timeout= 2.0) {
    $this->isConnected= true;
    return 1;
  }
  
  /**
   * Close
   *
   * @return  bool
   */
  public function close() {
    $this->isConnected= false;
    return true;
  } 
  
  /**
   * Set timeout
   *
   * @param   float timeout default 2.0
   */
  public function setTimeout($timeout= 2.0) {
    $this->_timeout= $timeout;
  }
  
  /**
   * Set blocking (NOOP)
   *
   * @param   bool blockMode
   */
  public function setBlocking($blockMode) {}
  
  /**
   * Check whether data is available
   *
   * @return  bool
   */
  public function canRead($timeout= null) {
    return 0 < sizeof($this->_data);
  }
  
  /**
   * Read
   *
   * @param   int len
   * @return  string
   */
  public function read($len= 4096) {
    if (0 == sizeof($this->_data)) return null;
    return array_shift($this->_data);
  }
  
  /**
   * Read a line
   *
   * @param   int len
   * @return  string
   */
  public function readLine($len= 4096) {
    return $this->read($len);
  }
  
  /**
   * Read binary (NOOP)
   *
   * @param   int len
   * @return  string
   */
  public function readBinary($len= 4096) {}
  
  /**
   * Returns whether we're at the end of the data
   *
   * @return  bool
   */
  public function eof() {
    return 0 == sizeof($this->_data);
  }
}
