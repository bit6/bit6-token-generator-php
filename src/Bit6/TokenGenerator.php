<?php

namespace Bit6;

use \Firebase\JWT\JWT;

class TokenGenerator {
    private $_apiKey;
    private $_apiSecret;

    public function __construct($apiKey, $apiSecret) {
        if (!$apiKey || !$apiSecret) {
            throw new \Exception("API key and/or secret not specified");
        }
        $this->_apiKey = $apiKey;
        $this->_apiSecret = $apiSecret;
    }

    public function createToken($identities) {
        if (!$identities || count($identities) < 1) {
            throw new \Exception("Must specify at least one identity");
        }
        // Primary identity
        $primary = array_shift($identities);
        // Current time - Unix timestamp
        $now = time();
        // JWT claims
        $data = array (
            // Issued at
            'iat' => $now,
            // Expiration - 10 minutes
            'exp' => $now + 10*60,
            // Bit6 API key as audience claim
            'aud' => $this->_apiKey,
            // Primary identity as subject
            'sub' => $primary
        );
        // Handle additional identities
        if (count($identities) > 0) {
            $data['identities'] = $identities;
        }

        $jwt = JWT::encode($data, $this->_apiSecret);
        return $jwt;
    }
}
