<?php namespace webservices\json;

use text\Tokenizer;
use text\parser\generic\AbstractLexer;


/**
 * Lexer for JsonDecoder
 *
 */
class JsonLexer extends AbstractLexer {

  // Keywords used in JSON
  protected static
    $keywords  = array(
      'true'   => JsonParser::T_TRUE,
      'false'  => JsonParser::T_FALSE,
      'null'   => JsonParser::T_NULL,
    );

  const
    DELIMITERS        = "{}: \n\r\t[],\"\0",      // Default delimiters
    STRING_DELIMITERS = '"\\',                    // Delimiters used in strings
    ESCAPES           = '"\\/bfnrtu',             // Possible escapes \X
    HEX               = '0123456789abcdefABCDEF'; // Hex digits

  private
    $delimiters= self::DELIMITERS, // active delimiters
    $string= false;                // Inside string?

  /**
   * Constructor
   *
   * @param   text.Tokenizer source
   */
  public function __construct(Tokenizer $source) {
    $this->tokenizer= $source;
    $this->tokenizer->delimiters= self::DELIMITERS;
    $this->tokenizer->returnDelims= true;
    $this->position= array(1, 1);   // Y, X. Current postition. Used for debugging.
  }

  /**
   * Advance this 
   *
   * @return  bool
   */
  public function advance() {
    while ($hasMore= $this->tokenizer->hasMoreTokens()) {
      $token= $this->tokenizer->nextToken($this->delimiters);
      $this->value= $token;

      // Move position
      $l= substr_count($this->value, "\n");
      if ($l > 0) {
        $this->position[0]+= $l;
        $this->position[1]= strlen($this->value) - strrpos($this->value, "\n");
      } else {
        $this->position[1]+= strlen($this->value);
      }

      // If it is only a seperation character, continue
      if (strpos(" \n\r\t", $this->value) !== false && $this->string == false) {
        continue;
      } else {
        if (strlen($this->value) == 1 && strpos($this->delimiters, $this->value)!== false) {
          if ($this->value == '"') {
            // Start or end a string
            $this->token= ord($token);
            if ($this->string == true) {
              $this->delimiters= self::DELIMITERS;
              $this->string= false;
            } else {
              $this->delimiters= self::STRING_DELIMITERS;
              $this->string= true;
            }
          } else if ($this->value == '\\') {
            // Escape inside string
            $nextToken= $this->tokenizer->nextToken(self::ESCAPES);
            $this->value.= $nextToken;
            switch ($nextToken) {
              case '"'  :
                $this->token= JsonParser::T_ESCAPE_QUOTATION;
                break;
              case '\\' :
                $this->token= JsonParser::T_ESCAPE_REVERSESOLIDUS;
                break;
              case '/'  :
                $this->token= JsonParser::T_ESCAPE_SOLIDUS;
                break;
              case 'b'  :
                $this->token= JsonParser::T_ESCAPE_BACKSPACE;
                break;
              case 'f'  :
                $this->token= JsonParser::T_ESCAPE_FORMFEED;
                break;
              case 'n'  :
                $this->token= JsonParser::T_ESCAPE_NEWLINE;
                break;
              case 'r'  :
                $this->token= JsonParser::T_ESCAPE_CARRIAGERETURN;
                break;
              case 't'  :
                $this->token= JsonParser::T_ESCAPE_HORIZONTALTAB;
                break;
              case 'u'  :
                $this->token= JsonParser::T_ESCAPE_UNICODE;
                // A unicode character needs four hex digits
                for ($i= 0; $i < 4; $i++) {
                  $nextToken= $this->tokenizer->nextToken(self::HEX);
                  $this->value.= $nextToken;
                  if (strlen($nextToken) != 1 || (strpos(self::HEX, $nextToken) === false)) {
                    throw new JsonException('Unknown Escape: '.$this->value);
                  }
                }
                break;
              default   :
                // Throw exception, if no valid escape is given
                throw new JsonException('Unknown Escape: '.$this->value);
            }
          } else {
            // Turn delimiter charakter into its ascii position value.
            $this->token= ord($token);
          }
        } else if (isset(self::$keywords[$this->value]) && $this->string == false) {
          // Use keyword  
          $this->token= self::$keywords[$this->value];
        } else if (is_numeric($this->value) && $this->string == false) {
          if (
            preg_match('/^[\\-]?(([1-9]+[0-9]*)|0){1}$/', $this->value) == 1 &&
            doubleval($this->value) >= LONG_MIN &&
            doubleval($this->value) <= LONG_MAX
          ) {
            // Valid Integer turn into integer. (Neccessary fo testing correct.)
            $this->token= JsonParser::T_INT;
          } else if (
              preg_match(
                '/^[\\-]?(([1-9]+[0-9]*)|0){1}([.]{1}[0-9]+)?([eE]{1}[+\\-]?[0-9]+)?$/',
                $this->value
              ) == 1
            ) {
            // Valid Float turn into float. (Neccessary fo testing correct.)
            $this->token= JsonParser::T_FLOAT;
          } else {
            // Exception for wrong json numbers, but valid php numbers. e.g. '010'
            throw new JsonException('Not a valid number: '.$this->value);
          }
        } else {
          // If nothing happend before, it must be some kind of string.
          $this->token= JsonParser::T_STRING;
        }
      }
      break;
    }

    // Return if next token exists.
    return $hasMore;
  }
}
