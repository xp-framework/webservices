<?php namespace webservices\soap\types;



/**
 * Represents a double value.
 *
 */
class SOAPDouble extends \lang\Object implements SoapType {
  public
    $double;
    
  /**
   * Constructor
   *
   * @param   double double
   */  
  public function __construct($double) {
    $this->double= number_format($double, 0, false, false);
  }
  
  /**
   * Return a string representation for use in SOAP
   *
   * @return  string 
   */    
  public function toString() {
    return (string)$this->double;
  }
  
  /**
   * Returns this type's name
   *
   * @return  string
   */
  public function getType() {
    return 'xsd:double';
  }

  /**
   *
   */
  public function getItemName() {
    return false;
  }

  /**
   *
   */
  public function asSoapType() {
    return new SoapVar($this->double, XSD_DOUBLE);
  }
}
