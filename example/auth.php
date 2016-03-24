<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables from .env file
$dotenv = new Dotenv\Dotenv(__DIR__ . '/../');
$dotenv->load();

// Read Bit6 API key and secret from environment variables
$apiKey = getenv('BIT6_API_KEY');
$apiSecret = getenv('BIT6_API_SECRET');
error_log("API key=$apiKey, secret=$apiSecret");

// In this example, POST body contains JSON
// object with the identity URIs to use. In real life
// the identities should be provided by your application code.
$body = json_decode(file_get_contents('php://input'));

error_log('BODY: ' . print_r($body, true));

// Identities that this user will have
$identities = $body->identities;

// Create TokenGenerator
$tg = new Bit6\TokenGenerator($apiKey, $apiSecret);

// Generate a token
$token = $tg->createToken($identities);

// Return a JSON response
header('Content-Type: application/json;charset=utf-8');
echo '{"ext_token":' . $token . '}';
