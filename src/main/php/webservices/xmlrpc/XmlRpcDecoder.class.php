<?php namespace webservices\xmlrpc;

use xml\XMLFormatException;
use xml\Node;
use util\Date;
use util\Bytes;
use lang\XPClass;

/**
 * XML-RPC decoder
 *
 * @test     xp://net.xp_framework.unittest.scriptlet.rpc.XmlRpcDecoderTest
 * @see      http://xmlrpc.com
 * @purpose  Decode XML-RPC data
 */
class XmlRpcDecoder extends \lang\Object {

  /**
   * Decode XML node-set into the data structures they represent
   *
   * @param   xml.Node node
   * @return  var
   */
  public function decode(Node $node) {
    return $this->_unmarshall($node);
  }
    
  /**
   * Recursively deserialize data for the given node.
   *
   * @param   xml.Node node
   * @return  var
   * @throws  lang.IllegalArgumentException if the data cannot be deserialized
   * @throws  lang.ClassNotFoundException in case a XP object's class could not be loaded
   * @throws  xml.XMLFormatException
   */
  protected function _unmarshall(Node $node) {

    // Simple form: If no subnode indicating the type exists, the type
    // is string, e.g. <value>Test</value>
    if (!$node->hasChildren()) return (string)$node->getContent();

    // Long form - with subnode, the type is derived from the node's name,
    // e.g. <value><string>Test</string></value>.
    $c= $node->nodeAt(0);
    switch ($c->getName()) {
      case 'struct':
        $ret= [];
        foreach ($c->getChildren() as $child) {
          $data= [];
          $data[$child->nodeAt(0)->getName()]= $child->nodeAt(0);
          $data[$child->nodeAt(1)->getName()]= $child->nodeAt(1);
          $ret[$data['name']->getContent()]= $this->_unmarshall($data['value']);
          unset($data);
        }
        
        if (!isset($ret['__xp_class'])) return $ret;

        $class= XPClass::forName($ret['__xp_class']);
        $instance= $class->newInstance();
        foreach ($ret as $name => $value) {
          if (!$class->hasField($name)) continue;

          $field= $class->getField($name);
          if ($field->getModifiers() & MODIFIER_STATIC) continue;

          $class->getField($name)->setAccessible(true)->set($instance, $value);
        }
        return $instance;
        
      case 'array':
        $ret= [];
        foreach ($c->nodeAt(0)->getChildren() as $child) {
          $ret[]= $this->_unmarshall($child);
        }
        return $ret;
      
      case 'int': case 'i4':
        return (int)$c->getContent();
      
      case 'double':
        return (double)$c->getContent();
      
      case 'boolean':
        return (bool)$c->getContent();
      
      case 'string':
        return (string)$c->getContent();
      
      case 'dateTime.iso8601':
        return new Date($c->getContent());
      
      case 'nil':
        return null;

      case 'base64':
        return new Bytes(base64_decode($c->getContent()));
        
      default:
        throw new \lang\IllegalArgumentException('Could not decode node as its type is not supported: '.$c->getName());
    }
  }
}
