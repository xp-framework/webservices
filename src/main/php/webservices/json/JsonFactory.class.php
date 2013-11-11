<?php namespace webservices\json;

/**
 * Json decoder factory. Use this class to get instances
 * of decoders.
 *
 * This enables us to use bundled PHP extensions if they're
 * available or use the userland implementation as fallback.
 *
 * @see      http://json.org
 * @purpose  Factory
 */
class JsonFactory extends \lang\Object {

  /**
   * Create an instance of a decoder
   *
   * @return  webservices.json.IJsonDecoder
   */
  public static function create() {
    return new JsonDecoder();
  }
}
