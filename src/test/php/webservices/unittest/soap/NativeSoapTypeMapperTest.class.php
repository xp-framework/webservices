<?php namespace webservices\unittest\soap;

use unittest\TestCase;
use webservices\soap\native\NativeSoapTypeMapper;
use webservices\soap\Parameter;
use unittest\actions\RuntimeVersion;
use unittest\actions\VerifyThat;

/**
 * TestCase
 *
 * @see   xp://webservices.soap.native.NativeSoapTypeMapper
 */
class NativeSoapTypeMapperTest extends TestCase {
  protected $fixture= null;

  /**
   * Set up test and create fixture
   *
   */
  public function setUp() {
    if (!\lang\Runtime::getInstance()->extensionAvailable('soap')) {
      throw new \unittest\PrerequisitesNotMetError('PHP Soap extension not available', null, array('ext/soap'));
    }

    $this->fixture= new NativeSoapTypeMapper();
  }

  /**
   * Assertion helper
   *
   * @param   var expected
   * @param   var actual
   * @throws  unittest.AssertionFailedError
   */
  public function assertEqualSoapVar($expected, $actual) {
    if (\xp::stringOf($expected) !== \xp::stringOf($actual)) {
      $this->fail('not equal', $actual, $expected);
    }
  }

  #[@test, @action(new RuntimeVersion('<7.0.0-dev'))]
  public function boxInteger() {
    $this->assertEqualSoapVar(new \SoapVar(5, XSD_INTEGER), $this->fixture->box(new \lang\types\Integer(5)));
  }

  #[@test, @action(new RuntimeVersion('<7.0.0-dev'))]
  public function boxLong() {
    $this->assertEqualSoapVar(new \SoapVar('5', XSD_LONG), $this->fixture->box(new \lang\types\Long(5)));
  }

  #[@test, @action(new RuntimeVersion('<7.0.0-dev'))]
  public function boxDouble() {
    $this->assertEqualSoapVar(new \SoapVar(5.0, XSD_DOUBLE), $this->fixture->box(new \lang\types\Double(5)));
  }

  #[@test]
  public function boxParameter() {
    $this->assertEqualSoapVar(new \SoapParam('bar', 'foo'), $this->fixture->box(new Parameter('foo', 'bar')));
  }

  #[@test, @action([
  #  new RuntimeVersion('<7.0.0-dev'),
  #  new VerifyThat(function() { return !defined('HHVM_VERSION'); })
  #])]
  public function recursiveBoxParameter() {
    $this->assertEqualSoapVar(
      new \SoapParam(new \SoapVar(5, XSD_INTEGER), 'foo'),
      $this->fixture->box(new Parameter('foo', new \lang\types\Integer(5)))
    );
  }

  #[@test, @action(new RuntimeVersion('<7.0.0-dev'))]
  public function boxBoolean() {
    $this->assertEqualSoapVar(
      new \SoapVar(true, XSD_BOOLEAN),
      $this->fixture->box(new \lang\types\Boolean(true))
    );
  }

  #[@test, @action(new RuntimeVersion('<7.0.0-dev'))]
  public function boxBytes() {
    $this->assertEqualSoapVar(
      new \SoapVar(base64_encode('ABCDE'), XSD_BASE64BINARY),
      $this->fixture->box(new \lang\types\Bytes('ABCDE'))
    );
  }

  #[@test, @action(new RuntimeVersion('<7.0.0-dev'))]
  public function boxCharacter() {
    $this->assertEqualSoapVar(
      new \SoapVar('a', XSD_STRING),
      $this->fixture->box(new \lang\types\Character('a'))
    );
  }

  #[@test]
  public function boxDate() {
    $this->assertEqualSoapVar(
      new \SoapVar('1980-05-28T12:05:00+0200', XSD_DATETIME),
      $this->fixture->box(new \util\Date('1980-05-28T12:05:00+0200'))
    );
  }

  #[@test, @action(new RuntimeVersion('<7.0.0-dev'))]
  public function boxShort() {
    $this->assertEqualSoapVar(
      new \SoapVar('127', XSD_SHORT),
      $this->fixture->box(new \lang\types\Short(127))
    );
  }

  #[@test, @action(new RuntimeVersion('<7.0.0-dev'))]
  public function boxString() {
    $this->assertEqualSoapVar(
      new \SoapVar('Hello Soap World', XSD_STRING),
      $this->fixture->box(new \lang\types\String('Hello Soap World'))
    );
  }
}
