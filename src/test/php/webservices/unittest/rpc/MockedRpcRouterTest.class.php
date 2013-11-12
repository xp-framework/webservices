<?php namespace webservices\unittest\rpc;

/**
 * TestCase
 */
abstract class MockedRpcRouterTest extends \unittest\TestCase {

  /**
   * Check for existance of specific header
   *
   * @param   string[] headers
   * @return  string needle
   * @throws  unittest.AssertionFailedError
   */
  protected function assertHasHeader($headers, $needle) {
    foreach ($headers as $h) {
      if (false !== (strpos($h, $needle))) return;
    }
    
    $this->fail('Expected header not found', $headers, $needle);
  }
  
  /**
   * Check if one string contains another
   *
   * @param   
   * @return  
   */
  protected function assertStringContained(\lang\types\String $haystack, \lang\types\String $needle) {
    if (false === strpos((string)$needle, (string)$haystack)) {
      $this->fail('Expected sub-string not found', $needle, $haystack);
    }
  }
}
