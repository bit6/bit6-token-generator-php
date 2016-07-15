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

    // Process Identity URI with correct mailto protocol
    public function testNormalizeIndetityURIMailToCorrect()
    {
        // Indicate method
        fwrite(STDOUT, "\n". __METHOD__ . "\n");

        // Create identity
        $identity = "mailto:me@you.com";
        $expected = $identity;
        $actual = $this->tg->normalizeIdentityURI($identity);

        // Assert equals
        $this->assertEquals($expected, $actual);
    }

    // Process Identity URI with correct tel protocol
    public function testNormalizeIndetityURITelCorrect()
    {
        // Indicate method
        fwrite(STDOUT, "\n". __METHOD__ . "\n");

        // Create identity
        $identity = "tel:+12123331234";
        $expected = $identity;
        $actual = $this->tg->normalizeIdentityURI($identity);

        // Assert equals
        $this->assertEquals($expected, $actual);
    }

    // Process Identity URI with correct grp protocol
    public function testNormalizeIndetityURIGrpCorrect()
    {
        // Indicate method
        fwrite(STDOUT, "\n". __METHOD__ . "\n");

        // Create identity
        $identity = "grp:9de82b5b_236d_40f6_b5a2";
        $expected = $identity;
        $actual = $this->tg->normalizeIdentityURI($identity);

        // Assert equals
        $this->assertEquals($expected, $actual);
    }

    // Process Identity URI with correct uid protocol
    public function testNormalizeIndetityURIUidCorrect()
    {
        // Indicate method
        fwrite(STDOUT, "\n". __METHOD__ . "\n");

        // Create identity
        $identity = "uid:9de82b5b-236d-40f6-b5a2-e16f5d09651d";
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

    // Process Identity URI with correct tel protocol
    public function testCheckIdentities()
    {
        // Indicate method
        fwrite(STDOUT, "\n". __METHOD__ . "\n");

        // Create identity
        $identity = ["usr:john","tel:+12123331234"];
        $expected = $identity;
        $actual = $this->tg->checkIdentities($identity);

        // Assert equals
        $this->assertEquals($expected, $actual);
    }

    // Create token
    public function testCreateTokenWithString()
    {
        global $apiKey, $apiSecret;
      // Indicate method
        fwrite(STDOUT, "\n". __METHOD__ . "\n");

      // Create identity
        $identity = "usr:test";

      // JWT claims
        $now = time();
        $ttl = 1;
        $data = array (
          // Issued at
          'iat' => $now,
          // Expiration - 10 minutes
          'exp' => $now + $ttl*60,
          // Bit6 API key as audience claim
          'aud' => $apiKey,
          // Primary identity as subject
          'sub' => $identity
        );
        $expected = JWT::encode($data, $apiSecret);
        $actual = $this->tg->createToken($identity, $ttl, $now);

      // Assert equals
        $this->assertEquals($expected, $actual);
    }

    // Create token with Array
    public function testCreateTokenWithArray()
    {
        global $apiKey, $apiSecret;
      // Indicate method
        fwrite(STDOUT, "\n". __METHOD__ . "\n");

      // Create identity
        $identity = ["usr:john","tel:+12123331234"];

      // JWT claims
        $now = time();
        $ttl = 1;
        $clone = $identity;
        $data = array (
          // Issued at
          'iat' => $now,
          // Expiration - 10 minutes
          'exp' => $now + $ttl*60,
          // Bit6 API key as audience claim
          'aud' => $apiKey,
          // Primary identity as subject
          'sub' => array_shift($clone),
          //Other identities
          'identities' => $clone
        );
        $expected = JWT::encode($data, $apiSecret);
        $actual = $this->tg->createToken($identity, $ttl, $now);

      // Assert equals
        $this->assertEquals($expected, $actual);
    }
}
