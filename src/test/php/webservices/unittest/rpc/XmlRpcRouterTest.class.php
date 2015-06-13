<?php namespace webservices\unittest\rpc;

use webservices\unittest\rpc\mock\XmlRpcRouterMock;

/**
 * Test case for XmlRpcRpcRouter
 *
 * @see  xp://webservices.xmlrpc.rpc.XmlRpcRouter
 */
class XmlRpcRouterTest extends MockedRpcRouterTest {
  protected $router = null;

  /**
   * Setup test fixture
   *
   */
  public function setUp() {
    $this->router= new XmlRpcRouterMock('webservices.unittest.rpc.impl');
    $this->router->setMockMethod(\peer\http\HttpConstants::POST);
    $this->router->setMockData('<?xml version="1.0" encoding="utf-8"?>
      <methodCall>
        <methodName>DummyRpcImplementation.getImplementationName</methodName>
        <params/>
      </methodCall>
    ');
  }
  
  #[@test, @ignore('Process missing')]
  public function basicPostRequest() {
    $this->router->init();
    $response= $this->router->process();
    $this->assertEquals(200, $response->statusCode);
    $this->assertHasHeader($response->headers, 'Content-type: text/xml; charset=utf-8');
    
    $msg= \webservices\xmlrpc\XmlRpcResponseMessage::fromString($response->getContent());
    $this->assertEquals('webservices.unittest.rpc.impl.DummyRpcImplementationHandler', $msg->getData());
  }

  #[@test, @ignore('Process missing'), @expect('scriptlet.ScriptletException')]
  public function basicGetRequest() {
    $this->router->setMockMethod(\peer\http\HttpConstants::GET);
    $this->router->init();
    $response= $this->router->process();
  }
  
  #[@test, @ignore('Process missing')]
  public function callNonexistingClass() {
    $this->router->setMockData('<?xml version="1.0" encoding="utf-8"?>
      <methodCall>
        <methodName>ClassDoesNotExist.getImplementationName</methodName>
        <params/>
      </methodCall>
    ');
    
    $this->router->init();
    $response= $this->router->process();
    
    $this->assertEquals(500, $response->statusCode);
  }
  
  #[@test, @ignore('Process missing')]
  public function callNonexistingMethod() {
    $this->router->setMockData('<?xml version="1.0" encoding="utf-8"?>
      <methodCall>
        <methodName>DummyRpcImplementation.methodDoesNotExist</methodName>
        <params/>
      </methodCall>
    ');
    
    $this->router->init();
    $response= $this->router->process();
    
    $this->assertEquals(500, $response->statusCode);
  }

  #[@test, @ignore('Process missing')]
  public function callNonWebmethodMethod() {
    $this->router->setMockData('<?xml version="1.0" encoding="utf-8"?>
      <methodCall>
        <methodName>DummyRpcImplementation.methodExistsButIsNotAWebmethod</methodName>
        <params/>
      </methodCall>
    ');
    
    $this->router->init();
    $response= $this->router->process();
    
    $this->assertEquals(500, $response->statusCode);
  }

  #[@test, @ignore('Process missing')]
  public function callFailingMethod() {
    $this->router->setMockData('<?xml version="1.0" encoding="utf-8"?>
      <methodCall>
        <methodName>DummyRpcImplementation.giveMeFault</methodName>
        <params/>
      </methodCall>
    ');
    
    $this->router->init();
    $response= $this->router->process();
    $this->assertEquals(500, $response->statusCode);

    // Check for correct fault code
    $message= \webservices\xmlrpc\XmlRpcResponseMessage::fromString($response->getContent());
    $fault= $message->getFault();
    $this->assertEquals(403, $fault->getFaultcode());
  }
  
  #[@test, @ignore('Process missing')]
  public function multipleParameters() {
    $this->router->setMockData('<?xml version="1.0" encoding="utf-8"?>
      <methodCall>
        <methodName>DummyRpcImplementation.checkMultipleParameters</methodName>
        <params>
          <param>
            <value>
              <string>Lalala</string>
            </value>
          </param>
          <param>
            <value>
              <int>1</int>
            </value>
          </param>
          <param>
            <value>
              <array>
                <data>
                  <value><i4>12</i4></value>
                  <value><string>Egypt</string></value>
                  <value><boolean>0</boolean></value>
                  <value><i4>-31</i4></value>
                </data>
              </array>
            </value>
          </param>
          <param>
            <value>
              <struct>
                <member>
                  <name>lowerBound</name>
                  <value><i4>18</i4></value>
                </member>
                <member>
                  <name>upperBound</name>
                  <value><i4>139</i4></value>
                </member>
              </struct>
            </value>
          </param>
        </params>
      </methodCall>
    ');
    
    $this->router->init();
    $response= $this->router->process();
    $this->assertHasHeader($response->headers, 'Content-type: text/xml; charset=utf-8');
    $this->assertEquals(200, $response->statusCode);
    
    $msg= \webservices\xmlrpc\XmlRpcResponseMessage::fromString($response->getContent());
    $data= $msg->getData();
    $this->assertEquals('Lalala', $data[0]);
    $this->assertEquals(1, $data[1]);
    $this->assertEquals(array(12, 'Egypt', false, -31), $data[2]);
    $this->assertEquals(array('lowerBound' => 18, 'upperBound' => 139), $data[3]);
  }
}
