<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables from .env file
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

// Read Bit6 API key and secret from environment variables
$apiKey = getenv('BIT6_API_KEY');
$apiSecret = getenv('BIT6_API_SECRET');
