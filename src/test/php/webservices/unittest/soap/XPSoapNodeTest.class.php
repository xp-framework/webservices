<?php namespace webservices\unittest\soap;

use unittest\TestCase;
use webservices\soap\xp\XPSoapNode;
use webservices\soap\xp\XPSoapMapping;
use webservices\soap\Parameter;
use webservices\soap\types\SOAPHashMap;
use webservices\soap\types\SOAPDateTime;
use webservices\soap\types\SOAPLong;
use util\Binford;
use unittest\actions\RuntimeVersion;

/**
 * TestCase for XPSoapNode class
 */
class XPSoapNodeTest extends TestCase {
  private static $TZ;

  /** @return void */
  #[@beforeClass]
  public static function saveTZ() {
    self::$TZ= date_default_timezone_get();
    date_default_timezone_set('Europe/Berlin');
  }

  /** @return void */
  #[@afterClass]
  public static function restoreTZ() {
    date_default_timezone_set(self::$TZ);
  }

  /**
   * Helper method
   *
   * @param  var $object
   * @return webservices.soap.xp.XPSoapNode
   */
  protected function node($object) {
    return XPSoapNode::fromArray([$object], 'array', new XPSoapMapping())->nodeAt(0);
  }

  #[@test]
  public function simpleNull() {
    $this->assertEquals(
      new XPSoapNode('item', null, ['xsi:nil' => 'true']),
      $this->node(null)
    );
  }

  #[@test]
  public function simpleString() {
    $this->assertEquals(
      new XPSoapNode('item', 'my string', ['xsi:type' => 'xsd:string']),
      $this->node('my string')
    );
  }

  #[@test]
  public function simpleInteger() {
    $this->assertEquals(
      new XPSoapNode('item', 12345, ['xsi:type' => 'xsd:int']),
      $this->node(12345)
    );
  }

  #[@test]
  public function soapLong() {
    $this->assertEquals(
      new XPSoapNode('item', '12345', ['xsi:type' => 'xsd:long']),
      $this->node(new SOAPLong(12345))
    );
  }

  #[@test]
  public function namedParameter() {
    $this->assertEquals(
      new XPSoapNode('name', 'content', ['xsi:type' => 'xsd:string']),
      $this->node(new Parameter('name', 'content'))
    );
  }

  #[@test]
  public function simpleBoolean() {
    $this->assertEquals(
      new XPSoapNode('item', 'true', ['xsi:type' => 'xsd:boolean']),
      $this->node(true)
    );
  }

  #[@test]
  public function simpleDouble() {
    $this->assertEquals(
      new XPSoapNode('item', 5.0, ['xsi:type' => 'xsd:float']),
      $this->node(5.0)
    );
  }

  #[@test]
  public function simpleHashmap() {
    $this->assertEquals(
      (new XPSoapNode('item', null, ['xsi:type' => 'xsd:struct']))
        ->withChild(new XPSoapNode('key', 'value', ['xsi:type' => 'xsd:string'])),
      $this->node(['key' => 'value'])
    );
  }

  #[@test]
  public function soapHashmap() {
    $node= new XPSoapNode('item', '', ['xmlns:hash' => 'http://xml.apache.org/xml-soap', 'xsi:type' => 'hash:Map']);
    $node->addChild(new XPSoapNode('item'))
      ->withChild(new XPSoapNode('key', 'key', ['xsi:type' => 'xsd:string']))
      ->withChild(new XPSoapNode('value', 'value', ['xsi:type' => 'xsd:string']));

    $this->assertEquals(
      $node,
      $this->node(new SOAPHashMap(['key' => 'value']))
    );
  }

  #[@test]
  public function simpleArray() {
    $this->assertEquals(
      (new XPSoapNode('item', null, ['xsi:type' => 'SOAP-ENC:Array', 'SOAP-ENC:arrayType' => 'xsd:anyType[3]']))
        ->withChild(new XPSoapNode('item', 'one', ['xsi:type' => 'xsd:string']))
        ->withChild(new XPSoapNode('item', 'two', ['xsi:type' => 'xsd:string']))
        ->withChild(new XPSoapNode('item', 'three', ['xsi:type' => 'xsd:string'])),
      $this->node(['one', 'two', 'three'])
    );
  }

  #[@test]
  public function emptyArray() {
    $this->assertEquals(
      (new XPSoapNode('item', null, ['xsi:type' => 'xsd:struct', 'xsi:nil' => 'true'])),
      $this->node([])
    );
  }

  #[@test]
  public function simpleObject() {
    $this->assertEquals(
      (new XPSoapNode('item', null, ['xmlns:xp' => 'http://xp-framework.net/xmlns/xp', 'xsi:type' => 'xp:util.Binford']))
        ->withChild(new XPSoapNode('poweredBy', 6100, ['xsi:type' => 'xsd:int'])),
      $this->node(new Binford(6100))
    );
  }

  #[@test]
  public function simpleDate() {
    $this->assertEquals(
      new XPSoapNode('item', '1980-05-28T12:05:00+02:00', ['xsi:type' => 'xsd:dateTime']),
      $this->node(new \util\Date('1980-05-28 12:05:00+0200'))
    );
  }

  #[@test]
  public function soapDate() {
    $this->assertEquals(
      new XPSoapNode('item', '1980-05-28T12:05:00+02:00', ['xsi:type' => 'xsd:dateTime']),
      $this->node(new SOAPDateTime('1980-05-28 12:05:00+0200'))
    );
  }
}
