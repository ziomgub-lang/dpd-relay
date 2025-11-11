<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$url = 'https://cloud.dpd.com/api/v1/parcelshopfinder/postcode?country=DE&zipcode=10115';

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 25,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_SSL_VERIFYHOST => 2,
]);

$response = curl_exec($ch);
$info = curl_getinfo($ch);
$error = curl_error($ch);
curl_close($ch);

echo json_encode([
    'success' => empty($error),
    'url' => $url,
    'http_code' => $info['http_code'],
    'time' => $info['total_time'],
    'error' => $error,
    'preview' => substr($response, 0, 400)
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
