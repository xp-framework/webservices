<?php namespace webservices\json;

/**
 * Interface a Json decoder has to implement
 *
 * @ext      extension
 * @see      reference
 * @purpose  purpose
 */
interface IJsonDecoder {

  /**
   * Encode data into string
   *
   * @param   var data
   * @return  string
   */
  public function encode($data);
  
  /**
   * Decode string into data
   *
   * @param   string string
   * @return  var
   */
  public function decode($string);      

}
