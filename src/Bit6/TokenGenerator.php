<?php

namespace Bit6;

use \Firebase\JWT\JWT;

class TokenGenerator
{
    private $apiKey;
    private $apiSecret;

    public function __construct($apiKey, $apiSecret)
    {
        if (!$apiKey || !$apiSecret) {
            throw new \Exception("API key and/or secret not specified");
        }
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
    }

    /**
    * Create token for authentication process delegated to an external service
    * for example your own application server or an MBaaS platform
    * see http://docs.bit6.com/guides/auth/ for guide
    * see http://docs.bit6.com/guides/js/#managed for identity URI example
    * @param string|array $identities String or Array of identity URI
    * @param int $ttl Time before token expires (in minutes) (default - 10)
    * @param int $issued Unix time stamp token issued (default - current time)
    * @return string The token to be used in the Javascript SDK
    */
    public function createToken($identities, $ttl = 10, $issued = null)
    {
        // Ensure all identities match identity URI format
        // and convert string to array
        $identities = $this->checkIdentities($identities);
        // Primary identity
        $primary = array_shift($identities);
        // Current time - Unix timestamp
        if (!isset($issued)) {
            $issued = time();
        }
        // JWT claims
        $data = array (
            // Issued at
            'iat' => $issued,
            // Expiration - 10 minutes
            'exp' => $issued + $ttl*60,
            // Bit6 API key as audience claim
            'aud' => $this->apiKey,
            // Primary identity as subject
            'sub' => $primary
        );
        // Handle additional identities
        if (!empty($identities)) {
            $data['identities'] = $identities;
        }

        $jwt = JWT::encode($data, $this->apiSecret);
        return $jwt;
    }

    /**
    * Check to ensure all identities given are written in the proper URI format
    * @param string|array $identities String or Array of identity URI strings
    * @return array Array of identity URI strings that match identity URI format
    * @return Exception Indicate if no identities specified or meet criteria
    */
    public function checkIdentities($identities)
    {
      // Throw error if identities is empty
      // Used empty() instead of count. See http://bit.ly/29YV0Ho
        if (empty($identities)) {
            throw new \Exception("Must specify at least one identity");
        }

      // Convert to array if string
        if (is_string($identities)) {
            $identities = array($identities);
        }

      // Check all identities for correct format and normalize
        foreach ($identities as $key => &$identity) {
            // Normalize the identity (pulled from bit6.min.js)
            $normalized = $this->normalizeIdentityURI($identity);

            //Set new value or remove the item from the array
            if ($normalized) {
                $identity = $normalized;
            } else {
                unset($identities[$key]);
            }
        }

      // Return the normalized identities
        return $identities;
    }

    /**
    * Normalize the identity URI
    * @param string $identity The identity URI
    * @return string The normalized identity
    */
    public function normalizeIdentityURI($value)
    {
        $value = explode(':', $value);
        $protocol = trim(strtolower($value[0]));
        $data = $value[1];
      //Place holders
        $remove = $remain = null;
        switch ($protocol) {
            case "mailto":
                $data = strtolower($data);
                $remove = '/[^a-z0-9._%+-@]/';
                $remain = '/^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,8}$/';
                break;
            case "grp":
                $remove = '/[\s]/';
                $remain = '/[0-9a-zA-Z._]{22}/';
                break;
            case "tel":
                $remove = '/[^\\d+]/';
                $remain = '/^\+[1-9]{1}[0-9]{8,15}$/';
                break;
            case "uid":
                $data = strtolower($data);
                $remove = '/[^0-9a-f-]/';
                $remain =
                  '/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/';
                break;
            case "usr":
                $data = strtolower($data);
                $remove = '/[^a-z0-9.]/';
                $remain = '/^[a-z0-9.]+$/';
                break;
            default:
                // Protocol not matched
                trigger_error("Protocol not recognized", E_USER_WARNING);
                $remove = '/.*/';
        }
      // Remove unwanted pattern
        $data = preg_replace($remove, "", $data);

      // Return complete identity
        if ($data && preg_match($remain, $data)) {
            return "$protocol:$data";
        } else {
             return false;
        }
    }
}
