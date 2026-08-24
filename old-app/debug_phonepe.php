<?php

use App\Services\PhonePeService;
use PhonePe\Env;

$config = config('phonepe');
echo "Environment Config: " . $config['environment'] . "\n";
echo "Client ID: " . $config['client_id'] . "\n";
// Mask secret
echo "Client Secret: " . substr($config['client_secret'], 0, 4) . '****' . "\n";
echo "Client Version: " . $config['client_version'] . "\n";

$service = PhonePeService::getInstance();
$client = $service->getClient();

// Use reflection to peek at the client's envUrl which is protected/private usually, 
// but in the SDK it might be accessible or we can infer it.
// The SDK StandardCheckoutClient extends BaseClient.
// BaseClient has $env (string).

$reflection = new ReflectionClass($client);
// StandardCheckoutClient doesn't have env, BaseClient does? 
// Actually the getInstance factory sets the base URL.
// Let's look at the object dump.
dump($client);

// Try a raw check of the environment constant
echo "Env::UAT: " . Env::UAT . "\n";
echo "Env::PRODUCTION: " . Env::PRODUCTION . "\n";
