<?php namespace webservices\soap;

/**
 * Represents a single parameter to a SOAP call.
 *
 * @purpose  Wrapper
 */
class Parameter extends \lang\Object {
  public
    $name     = '',
    $value    = null;

  /**
   * Constructor
   *
   * @param   string name
   * @param   var value default NULL
   */
  public function __construct($name, $value= null) {
    $this->name= $name;
    $this->value= $value;
  }

  /**
   * Creates a string representation of this image object
   *
   * @return  string
   */
  public function toString() {
    return sprintf(
      '%s@(%s) {%s}',
      $this->getClassName(),
      $this->name,
      ($this->value instanceof \lang\Generic 
        ? $this->value->toString() 
        : var_export($this->value, 1)
      )
    );
  }
}
