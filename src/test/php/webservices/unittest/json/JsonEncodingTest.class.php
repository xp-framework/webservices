<?php namespace webservices\unittest\json;

use unittest\TestCase;
use util\Date;
use webservices\json\JsonDecoder;

/**
 * Testcase for JsonDecoder
 *
 * @see   xp://webservices.json.JsonDecoder
 */
class JsonEncodingTest extends TestCase {
  protected $fixture= null;
  protected $tz= null;
  protected $prec= null;
      
  /**
   * Setup text fixture
   *
   * @return void
   */
  public function setUp() {
    $this->fixture= new JsonDecoder();
    $this->tz= date_default_timezone_get();
    $this->prec= ini_set('precision', 14);
    date_default_timezone_set('Europe/Berlin');
  }
  
  /**
   * Tear down test.
   *
   * @return void
   */
  public function tearDown() {
    date_default_timezone_set($this->tz);
    ini_set('precision', $this->prec);
  }

  /**
   * Returns encoded object
   *
   * @param   var input
   * @return  string
   */
  protected function encode($input) {
    return $this->fixture->encode($input);
  }
  
  #[@test]
  public function encodeString() {
    $this->assertEquals('"foo"', $this->encode('foo'));
  }
  
  #[@test]
  public function encodeUTF8String() {
    $this->assertEquals('"f\u00f6o"', $this->encode('föo'));
  }

  #[@test]
  public function encodeQuotationMarkString() {
    $this->assertEquals('"f\\"o\\"o"', $this->encode('f"o"o'));
  }

  #[@test]
  public function encodeReverseSolidusString() {
    $this->assertEquals('"fo\\\\o"', $this->encode('fo\\o'));
  }

  #[@test]
  public function encodeSolidusString() {
    $this->assertEquals('"fo\\/o"', $this->encode('fo/o'));
  }

  #[@test]
  public function encodeBackspaceString() {
    $this->assertEquals('"fo\\bo"', $this->encode('fo'."\x08".'o'));
  }

  #[@test]
  public function encodeFormfeedString() {
    $this->assertEquals('"fo\\fo"', $this->encode('fo'."\x0c".'o'));
  }

  #[@test]
  public function encodeNewlineString() {
    $this->assertEquals('"fo\\no"', $this->encode('fo'."\n".'o'));
  }

  #[@test]
  public function encodeCarriageReturnString() {
    $this->assertEquals('"fo\\ro"', $this->encode('fo'."\r".'o'));
  }

  #[@test]
  public function encodeHorizontalTabString() {
    $this->assertEquals('"fo\\to"', $this->encode('fo'."\t".'o'));
  }

  #[@test]
  public function encodePositiveSmallInt() {
    $this->assertEquals('1', $this->encode(1));
  }

  #[@test]
  public function encodeNegativeSmallInt() {
    $this->assertEquals('-1', $this->encode(-1));
  }

  #[@test]
  public function encodePositiveBigInt() {
    $this->assertEquals('2147483647', $this->encode(2147483647));
  }
  
  #[@test]
  public function encodeNegativeBigInt() {
    $this->assertEquals('-2147483647', $this->encode(-2147483647));
  }

  #[@test]
  public function encodeIntegerFloat() {
    $this->assertEquals('1', $this->encode(1.0));
  }

  #[@test]
  public function encodeSmallPositiveFloat() { 
    $this->assertEquals('1.1', $this->encode(1.1));
  }
  
  #[@test]
  public function encodeFloat() { 
    $this->assertEquals('-1.1', $this->encode(-1.1));
  }

  #[@test]
  public function encodeBigPositiveFloat() { 
    $this->assertEquals('9999999999999.1', $this->encode(9999999999999.1));
  }

  #[@test]
  public function encodeBigNevativeFloat() { 
    $this->assertEquals('-9999999999999.1', $this->encode(-9999999999999.1));
  }

  #[@test]
  public function encodeVerySmallFloat() { 
    $this->assertEquals('1.0E-11', $this->encode(0.00000000001));
  }

  #[@test]
  public function encodeAlmostVerySmallFloat() { 
    $this->assertEquals('0.123456789', $this->encode(0.123456789));
  }

  #[@test]
  public function encodeNull() {
    $this->assertEquals('null', $this->encode(null));
  }

  #[@test]
  public function encodeTrue() {
    $this->assertEquals('true', $this->encode(true));
  }

  #[@test]
  public function encodeFalse() {
    $this->assertEquals('false', $this->encode(false));
  }
  
  #[@test]
  public function encodeEmptyArray() {
    $this->assertEquals('[ ]', $this->encode(array()));
  }

  #[@test]
  public function encodeSimpleNumericArray() {
    $this->assertEquals(
      '[ 1 , 2 , 3 ]',
      $this->encode(array(1, 2, 3))
    );
  }

  #[@test]
  public function encodeSimpleMixedArray() {
    $this->assertEquals(
      '[ "foo" , 2 , "bar" ]',
      $this->encode(array('foo', 2, 'bar'))
    );
  }

  #[@test]
  public function encodeNormalMixedArray() {
    $this->assertEquals(
      '[ "foo" , 0.001 , false , [ 1 , 2 , 3 ] ]',
      $this->encode(array('foo', 0.001, false, array(1, 2, 3)))
    );
  }
     
  #[@test]
  public function encodeSimpleHashmap() {
    $this->assertEquals(
      '{ "foo" : "bar" , "bar" : "baz" }',
      $this->encode(array('foo' => 'bar', 'bar' => 'baz'))
    );
  }

  #[@test]
  public function encodeComplexMixedArray() {
    $this->assertEquals(
     '[ "foo" , true , { "foo" : "bar" , "0" : 2 } ]',
     $this->encode(array('foo', true, array('foo' => 'bar', 2)))
    );
  }

  #[@test]
  public function encodeComplexHashmap() {
    $this->assertEquals(
      '{ "foo" : "bar" , "3" : 0.123 , "4" : false , "array" : [ 1 , "foo" , false ] , '.
      '"array2" : { "0" : true , "bar" : 4 } , "array3" : { "foo" : { "foo" : "bar" } } }',
      $this->encode(array('foo' => 'bar',
        3 => 0.123,
        false,
        "array" => array(1, "foo", false),
        "array2" => array(true, "bar" => 4),
        "array3" => array("foo" => array("foo" => "bar"))
      ))
    );
  }

  #[@test, @expect(\webservices\json\JsonException::class)]
  public function encodeFileResource() {
    $this->encode(STDERR);
  }

  #[@test]
  public function encodeOneElementArray() {
    $this->assertEquals('[ "foo" ]', $this->encode(array('foo')));
  }

  #[@test]
  public function encodeOneElementObejct() {
    $this->assertEquals(
      '{ "foo" : "bar" }',
      $this->encode(array('foo' => 'bar'))
    );
  }

  #[@test]
  public function encodeStringObject() {
    $this->assertEquals(
      '"foobar"',
      $this->encode(new \lang\types\String('foobar'))
    );
  }

  #[@test]
  public function encodeStringObjectWithEscape() {
    $this->assertEquals(
      '"foobar\n"',
      $this->encode(new \lang\types\String("foobar\n"))
    );
  }

  #[@test]
  public function encodeStringObjectWithUmlat() {
    $this->assertEquals(
      '"E\u00fcro"',
      $this->encode(new \lang\types\String('Eüro'))
    );
  }

  #[@test]
  public function encodeStringObjectWithEuroSign() {
    $this->assertEquals(
      '"\u20acuro"',
      $this->encode(new \lang\types\String("\xe2\x82\xacuro", 'utf-8'))
    );
  }

  #[@test]
  public function encodeObject() {
    $this->assertEquals(
      '{ "prop" : "prop" , "__id" : null }',
      $this->encode(newinstance('lang.Object', array(), '{
        public $prop= "prop";
      }'))
    );
  }

  #[@test]
  public function encodeObjectWithPrivateProperty() {
    $this->assertEquals(
      '{ "prop" : "prop" , "__id" : null }',
      $this->encode(newinstance('lang.Object', array(), '{
        public $prop= "prop";
        private $priv= "priv";
      }'))
    );
  }

  #[@test]
  public function encodeObjectWithProtectedProperty() {
    $this->assertEquals(
      '{ "prop" : "prop" , "__id" : null }',
      $this->encode(newinstance('lang.Object', array(), '{
        public $prop= "prop";
        protected $prot= "prot";
      }'))
    );
  }

  #[@test]
  public function encodeDateObject() {
    $this->assertEquals(
      '{ "value" : "2009-05-18 01:02:03+0200" , "__id" : null }',
      $this->encode(new Date('2009-05-18 01:02:03'))
    );
  }

  #[@test]
  public function encodeAssociativeArrayWithModifiedPointerPosition() {
    $values= array("10" => "first", "15"=>"second", "22"=>"third");
    next($values);

    $this->assertEquals(
      '{ "10" : "first" , "15" : "second" , "22" : "third" }',
      $this->encode($values)
    );
  }
}
