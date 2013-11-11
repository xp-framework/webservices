<?php namespace webservices\soap\types;



/**
 * Represents a decimal value.
 *
 */
class SOAPDecimal extends \lang\Object implements SoapType {
  public
    $decimal;
    
  /**
   * Constructor
   *
   * @param   int value
   * @param   int decimal
   */  
  public function __construct($value, $decimal) {
    $this->decimal= number_format($value, $decimal, '.', false);
  }
  
  /**
   * Return a string representation for use in SOAP
   *
   * @return  string 
   */    
  public function toString() {
    return (string)$this->decimal;
  }
  
  /**
   * Returns this type's name
   *
   * @return  string
   */
  public function getType() {
    return 'xsd:decimal';
  }

  /**
   * Retrieve type as native SOAP type
   *
   * @return  php.SoapVar
   */
  public function asSoapType() {
    return new SoapVar($this->decimal, XSD_DOUBLE);
  }
}
