<?php namespace webservices\soap\types;



/**
 * Represents a long value. This class can be used to circumvent
 * the problem that some strong typed languages cannot cast ints
 * into a long (as PHP does automagically).
 *
 */
class SOAPLong extends \lang\Object implements SoapType {
  public
    $long;
    
  /**
   * Constructor
   *
   * @param   int long
   */  
  public function __construct($long) {
    $this->long= number_format($long, 0, false, false);
  }
  
  /**
   * Return a string representation for use in SOAP
   *
   * @return  string 
   */    
  public function toString() {
    return (string)$this->long;
  }
  
  /**
   * Returns this type's name
   *
   * @return  string
   */
  public function getType() {
    return 'xsd:long';
  }

  /**
   * Retrieve item name
   *
   * @return  mixed
   */
  public function getItemName() {
    return false;
  }

  /**
   * Retrieve type as native SOAP type
   *
   * @return  php.SoapVar
   */
  public function asSoapType() {
    return new SoapVar($this->long, XSD_LONG);
  }
}
