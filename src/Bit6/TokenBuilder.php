<?php

namespace Bit6;

use \Firebase\JWT\JWT;

class TokenBuilder {
    private $_keyId;
    private $_keySecret;

    public function __construct($keyId, $keySecret) {
        $this->_keyId = $keyId;
        $this->_keySecret = $keySecret;
        $this->props = new \stdClass;
        $this->props->grants = array();
        // Bit6 API default values
        $this->props->env = 'prod';
        $this->props->access = 'client';
    }

    public function access($access) {
        $this->props->access = $access;
        return $this;
    }

    public function aud($aud) {
        $this->props->aud = $aud;
        return $this;
    }

    public function device($device) {
        $this->props->device = $device;
        return $this;
    }

    public function env($env) {
        $this->props->env = $env;
        return $this;
    }

    public function exp($exp) {
        $this->props->exp = $exp;
        return $this;
    }

    public function grant($key, $val) {
        $this->props->grants[$key] = $val;
        return $this;
    }

    public function grants($grants) {
        $this->props->grants = $grants;
        return $this;
    }

    public function identity($identity) {
        $this->props->identity = $identity;
        return $this;
    }

    public function key($id, $secret) {
        $this->_keyId = $id;
        $this->_keySecret = $secret;
        return $this;
    }

    public function profile($profile) {
        $this->props->profile = $profile;
        return $this;
    }

    public function ttl($ttl) {
        $this->props->ttl = $ttl;
        return $this;
    }



    public function build() {
        if (empty($this->_keyId) || empty($this->_keySecret)) {
            throw new \Exception('API Key or Secret not specified');
        }

        $p = $this->props;
        $sub = null;
        if ($p->access == 'client') {
            if (empty($p->identity) || empty($p->device)) {
                throw new \Exception('Identity and/or Device not specified');
            }
            $sub = $p->identity . '/' . $p->device;
        }
        // Determine the audience (if not specified directly)
        $aud = $p->aud;
        if  (!$aud) {
            // Audience is the Base API URL
            $aud = 'https://api.' . $p->env . '.bit6.com/' . $p->access;
        }
        // Current time - Unix timestamp
        $now = time();
        // JWT claims
        $claims = array (
            // Base API URL as the audience claim
            'aud' => $aud,
            // Key ID
            'iss' => $this->_keyId,
            // Issued at
            'iat' => $now,
            // Permission grants
            'grants' => $p->grants
        );
        // Identity/device as subject - optional
        if ($sub) {
            $claims['sub'] = $sub;
        }
        // Profile info - optional
        if ($p->profile) {
            $claims['profile'] = $p->profile;
        }
        // Set expiration claim - optional
        if ($p->exp) {
            $claims['exp'] = $p->exp;
        }
        // Set expiration claim based in time-to-live in seconds - optional
        else if ($p->ttl) {
            $claims['exp'] = $now + $p->ttl;
        }
        // Encode and sign the JWT token
        $jwt = JWT::encode($claims, $this->_keySecret);
        return $jwt;
    }

    public static function create($keyId=null, $keySecret=null) {
        return new TokenBuilder($keyId, $keySecret);
    }
}
