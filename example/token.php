<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables from .env file
$dotenv = new Dotenv\Dotenv(__DIR__ . '/../');
$dotenv->load();

// Read Bit6 API Key ID and Secret from environment variables
$keyId = getenv('BIT6_KEY_ID');
$keySecret = getenv('BIT6_KEY_SECRET');
error_log("API key id=$keyId, secret=$keySecret");

# By default, use form-urlencoded POST
$body = $_POST;
// Check if request body contains JSON
$type = $_SERVER['HTTP_CONTENT_TYPE'];
if ('application/json' == $type) {
    $body = json_decode( file_get_contents('php://input'), true );
}
error_log('BODY: ' . print_r($body, true));

$identity = $body['identity'];
$device = $body['device'];

// Grant permissions to access Signal, Video, and Chat service
$grants = array(
    'chat' => true,
    'signal' => true,
    'video'=> true
);
// Expire the token in 1 hour (ttl is in seconds)
$ttl = 60 * 60;

// Build the token
$token = Bit6\TokenBuilder::create()
    ->key($keyId, $keySecret)
    ->env('dev')
    ->access('client')
    ->grants($grants)
    ->identity($identity)
    ->device($device)
    ->ttl($ttl)
    ->build();

// Return a JSON response
header('Content-Type: application/json;charset=utf-8');
echo '{"token":"' . $token . '"}';
