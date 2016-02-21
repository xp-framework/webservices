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
      throw new \unittest\PrerequisitesNotMetError('PHP Soap extension not available', null, ['ext/soap']);
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

  #[@test]
  public function boxParameter() {
    $this->assertEqualSoapVar(new \SoapParam('bar', 'foo'), $this->fixture->box(new Parameter('foo', 'bar')));
  }

  #[@test]
  public function boxDate() {
    $this->assertEqualSoapVar(
      new \SoapVar('1980-05-28T12:05:00+0200', XSD_DATETIME),
      $this->fixture->box(new \util\Date('1980-05-28T12:05:00+0200'))
    );
  }
}
