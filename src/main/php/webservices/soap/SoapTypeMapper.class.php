<?php namespace webservices\soap;

use lang\Type;

/**
 * Maps primitive or boxed primitives to soap types
 *
 */
abstract class SoapTypeMapper extends \lang\Object {
  protected
    $handler= array(
      'webservices\\soap\\Parameter' => 'boxParameter',
      'SoapType'                     => 'boxSoapType',
      'lang\\types\\String'          => 'boxString',
      'lang\\types\\Long'            => 'boxLong',
      'lang\\types\\Integer'         => 'boxInteger',
      'lang\\types\\Short'           => 'boxShort',
      'lang\\types\\Double'          => 'boxDouble',
      'lang\\types\\Boolean'         => 'boxBoolean',
      'lang\\types\\Bytes'           => 'boxBytes',
      'lang\\types\\Character'       => 'boxCharacter',
      'util\\Date'                   => 'boxDate'
    );

  /**
   * Check if type of object is supported
   *
   * @param   lang.Generic object
   * @return  boolean
   */
  public function supports($object) {
    foreach ($this->handler as $class => $handler) {
      if ($object instanceof $class) return true;
    }

    return false;
  }

  /**
   * Box parameter into soap equivalent
   *
   * @param   lang.Generic object
   * @return  mixed
   * @throws  lang.IllegalArgumentException if type is not supported
   */
  public function box($object) {
    foreach ($this->handler as $class => $handler) {
      if (!$object instanceof $class) continue;
      return call_user_func([$this, $handler], $object);
    }

    throw new \lang\IllegalArgumentException('Type '.\xp::typeOf($object).' is not supported.');
  }

  /**
   * Box named parameter
   *
   * @param   webservices.soap.Parameter
   * @return  mixed
   */
  protected abstract function boxParameter($object);

  /**
   * Box SoapType
   *
   * @param   webservices.soap.types.SoapType object
   * @return  mixed
   */
  protected abstract function boxSoapType($object);

  /**
   * Box string
   *
   * @param   lang.types.String object
   * @return  mixed
   */
  protected abstract function boxString($object);

  /**
   * Box long
   *
   * @param   lang.types.Long object
   * @return  mixed
   */
  protected abstract function boxLong($object);

  /**
   * Box integer
   *
   * @param   lang.types.Integer object
   * @return  mixed
   */
  protected abstract function boxInteger($object);

  /**
   * Box short
   *
   * @param   lang.types.Short object
   * @return  mixed
   */
  protected abstract function boxShort($object);

  /**
   * Box double
   *
   * @param   lang.types.Double object
   * @return  mixed
   */
  protected abstract function boxDouble($object);

  /**
   * Box boolean
   *
   * @param   lang.types.Boolean object
   * @return  mixed
   */
  protected abstract function boxBoolean($object);

  /**
   * Box bytes
   *
   * @param   lang.types.Bytes object
   * @return  mixed
   */
  protected abstract function boxBytes($object);

  /**
   * Box character
   *
   * @param   lang.types.Character object
   * @return  mixed
   */
  protected abstract function boxCharacter($object);

  /**
   * Box date
   *
   * @param   util.Date object
   * @return  mixed
   */
  protected abstract function boxDate($object);
}
