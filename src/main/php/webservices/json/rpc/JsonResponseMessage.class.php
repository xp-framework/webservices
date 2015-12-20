<?php namespace webservices\json\rpc;

use webservices\json\JsonFactory;

/**
 * JSON response message
 *
 * @see      http://json-rpc.org
 * @purpose  Wrap JSON response message
 */
class JsonResponseMessage extends JsonMessage {

  /**
   * Create message from string representation
   *
   * @param   string string
   * @return  webservices.json.rpc.JsonResponseMessage
   */
  public static function fromString($string) {
    $decoder= JsonFactory::create();

    $msg= new JsonResponseMessage();
    $data= $decoder->decode($string);

    $msg->data= $data;
    $msg->id= $data['id'];
    return $msg;
  }
  
  /**
   * Create new message
   *
   * @param   string method
   * @param   int id
   */
  public function create($msg= null) {
    $this->id= $msg->getId();
  }
  
  /**
   * Set the data for the message
   *
   * @param   var data
   */
  public function setData($data) {
    $this->data= [
      'result'  => $data,
      'error'   => null,
      'id'      => $this->id
    ];
  }
  
  /**
   * Get data
   *
   * @return  var
   */
  public function getData() {
    return $this->data['result'];
  }    
  
  /**
   * Set a fault for the message
   *
   * @param   string faultCode
   * @param   string faultString
   */
  public function setFault($faultCode, $faultString) {
    $this->data= [
      'result'  => false,
      'error'   => [
        'faultCode'   => $faultCode,
        'faultString' => $faultString
      ],
      'id'      => $this->id
    ];
  }    
}
