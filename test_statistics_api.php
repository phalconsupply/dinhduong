<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');

// Create a test request to the statistics endpoint
$request = Illuminate\Http\Request::create('/admin/statistics/get-weight-for-age', 'GET');

// Mock authentication (use first user)
$user = App\Models\User::first();
if (!$user) {
    echo "ERROR: No users in database!\n";
    exit(1);
}

Auth::login($user);
echo "Logged in as: {$user->name} (ID: {$user->id})\n\n";

try {
    $response = $kernel->handle($request);
    $content = $response->getContent();
    
    echo "Response Status: " . $response->getStatusCode() . "\n";
    echo "Response Length: " . strlen($content) . " bytes\n\n";
    
    if ($response->getStatusCode() === 200) {
        $json = json_decode($content, true);
        if ($json) {
            echo "JSON Response:\n";
            echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
        } else {
            echo "Response Content (first 500 chars):\n";
            echo substr($content, 0, 500) . "\n";
        }
    } else {
        echo "Error Response:\n";
        echo substr($content, 0, 1000) . "\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " (Line " . $e->getLine() . ")\n";
    echo "\nStack Trace:\n" . $e->getTraceAsString() . "\n";
}

$kernel->terminate($request, $response ?? null);
