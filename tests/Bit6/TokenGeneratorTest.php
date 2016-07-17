<?php

namespace Bit6;

use \Firebase\JWT\JWT;

/**
 * Test class for TokenGenerator
 */
class TokenGeneratorTest extends \PHPUnit_Framework_TestCase
{
    protected $tg;

    protected function setUp()
    {
        global $apiKey, $apiSecret;
        $this->tg = new TokenGenerator($apiKey, $apiSecret);
    }

    /**
    * Check for error message with missing parameters
    * @expectedException Exception
    */
    public function testFailedConstructor()
    {
        new TokenGenerator("", "");
    }

    /**
     * Process Identity URI with correct protocol
     * @dataProvider stringURIProvider
     */
    public function testNormalizeIndetity($identity)
    {
        // Indicate method
        fwrite(STDOUT, "\n". __METHOD__ . "\n");

        //Check if data in identity is to be made lowercase
        $lowerCaseURIs = ["uid", "mailto", "usr"];
        $protocol = explode(":", $identity)[0];
        if(in_array($protocol, $lowerCaseURIs)){
          $identity = strtolower($identity);
        }

        //Set expectation
        $expected = $identity;
        $actual = $this->tg->normalizeIdentityURI($identity);

        // Assert equals
        $this->assertEquals($expected, $actual);
    }

  /**
   * Process Identity URI with unknown protocol
   * @expectedException PHPUnit_Framework_Error
   */
    public function testNormalizeIndetityUnknownProtocol()
    {
        // Indicate method
        fwrite(STDOUT, "\n". __METHOD__ . "\n");

        // Create identity
        $identity = "unknown:user";
        $actual = $this->tg->normalizeIdentityURI($identity);
    }

    /**
     * Check that all identities in correct format is returned using array
     */
    public function testCheckIdentitiesReturnAll()
    {
        // Indicate method
        fwrite(STDOUT, "\n". __METHOD__ . "\n");

        //Create Identity
        $identity = ["usr:john","tel:+12123331234"];
        $expected = $identity;
        $actual = $this->tg->checkIdentities($identity);

        // Assert equals
        $this->assertEquals($expected, $actual);
    }

    /**
     * Check that error thrown when empty identity string given
     * @expectedException Exception
     */
    public function testCheckIdentitiesErrorWhenEmptyString()
    {
        // Indicate method
        fwrite(STDOUT, "\n". __METHOD__ . "\n");
        $actual = $this->tg->checkIdentities([]);
    }

    /**
     * Check that error thrown when empty identity array given
     * @expectedException Exception
     */
    public function testCheckIdentitiesErrorWhenEmptyArray()
    {
        // Indicate method
        fwrite(STDOUT, "\n". __METHOD__ . "\n");
        $actual = $this->tg->checkIdentities("");
    }

    // Check that only identities in correct format is returned using array
    public function testCheckIdentitiesReturnOnlyValid()
    {
        // Indicate method
        fwrite(STDOUT, "\n". __METHOD__ . "\n");

        // Create identity
        $identity = ["usr:john","unknown:user"];
        // Get valid identities by suppressing warning about unknown protocol
        $actual = @$this->tg->checkIdentities($identity);
        array_pop($identity);
        $expected = $identity;

        // Assert equals
        $this->assertEquals($expected, $actual);
    }

    /**
     * Check that error thrown when argument is not a string or array
     * @expectedException PHPUnit_Framework_Error
     */
    public function testNormalizeOptionsWithIntegerExpectError()
    {
        // Indicate method
        fwrite(STDOUT, "\n". __METHOD__ . "\n");
        $actual = $this->tg->normalizeOptions(6);
    }

    /**
     * Create Tokens with Identity URIs as strings
     * @dataProvider stringURIProvider
     */
    public function testCreateTokenWithURIString($identity)
    {
        global $apiKey, $apiSecret;
      // Indicate method
        fwrite(STDOUT, "\n". __METHOD__ . "\n");

        //Check if data in identity is to be made lowercase
        $lowerCaseURIs = ["uid", "mailto", "usr"];
        $protocol = explode(":", $identity)[0];
        if(in_array($protocol, $lowerCaseURIs)){
          $identity = strtolower($identity);
        }

        //Set expectation
        $expected = $identity;
        $result = $this->tg->createToken($identity);
        $actual = (array) JWT::decode($result, $apiSecret, array('HS256'));

      // Assert equals
        $this->assertEquals($expected, $actual['sub']);
    }

    /**
     * Create token using array
     */
    public function testCreateTokenWithURIArray()
    {
        global $apiKey, $apiSecret;
      // Indicate method
        fwrite(STDOUT, "\n". __METHOD__ . "\n");

      // Create identity
        $identity = ["usr:john","tel:+12123331234"];

      // Set expectation
        $expected = $identity;
        $result = $this->tg->createToken($identity);
        $actual = (array) JWT::decode($result, $apiSecret, array('HS256'));

      // Assert equals
        $this->assertEquals(array_shift($expected), $actual['sub']);
        $this->assertEquals($expected, $actual['identities']);
    }

    // Create token using option associative array
    public function testCreateTokenWithOptionsArray()
    {
        global $apiKey, $apiSecret;
      // Indicate method
        fwrite(STDOUT, "\n". __METHOD__ . "\n");

      // JWT claims
        $identity = "usr:test";
        $now = time();
        $expires = $now + 1*60;
        $data = array (
          // Issued at
          'iat' => $now,
          // Expiration - 1 minute from now
          'exp' => $expires,
          // Bit6 API key as audience claim
          'aud' => $apiKey,
          // Primary identity as subject
          'sub' => $identity
        );
       // Options array
        $options = [
          'identities' => $identity,
          'issued' => $now,
          'expires' => $expires
        ];
        $expected = JWT::encode($data, $apiSecret);
        $actual = $this->tg->createToken($options);

      // Assert equals
        $this->assertEquals($expected, $actual);
    }

    // Provide Indentity URIs as string
    public function stringURIProvider()
    {
        return [
            'Email Address'  => ["mailto:me@you.com"],
            'Group' => ["grp:9de82b5b_236d_40f6_b5a2"],
            'Telephone Number' => ["tel:+12123331234"],
            'Unique ID'  => ["uid:9de82b5b-236d-40f6-b5a2-e16f5d09651d"],
            'User' => ["usr:testuser123"],
            'User with upper case' => ["usr:Test.User.123"]
        ];
    }
}
