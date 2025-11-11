<?php
/**
 * Universal DPD Cloud Relay
 * Obsługuje wszystkie endpointy API v1
 * Autor: ChatGPT + das sad (2025)
 */

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=utf-8");

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Konfiguracja połączenia DPD Cloud
$DPD_API_BASE = "https://cloud.dpd.com/api/v1/";

// Mapowanie ścieżki z relay na DPD Cloud
$path = $_SERVER['REQUEST_URI'];
$path = preg_replace("#^/api/v1/#", "", $path); // usuń prefix /api/v1/
$target_url = $DPD_API_BASE . $path;

// Obsługa metody i danych
$method = $_SERVER['REQUEST_METHOD'];
$input = file_get_contents("php://input");

// Przygotowanie zapytania cURL
$ch = curl_init($target_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
curl_setopt($ch, CURLOPT_TIMEOUT, 60);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);

// Obsługa POST/GET/DELETE
switch ($method) {
    case "POST":
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $input);
        break;
    case "PUT":
    case "PATCH":
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $input);
        break;
    case "DELETE":
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        break;
    default:
        // GET — nic nie zmieniamy
        break;
}

$response = curl_exec($ch);
$info = curl_getinfo($ch);
$error = curl_error($ch);
curl_close($ch);

// Odpowiedź w JSON
echo json_encode([
    "relay" => "dpd-relay.onrender.com",
    "target_url" => $target_url,
    "method" => $method,
    "http_code" => $info["http_code"],
    "success" => $error === "",
    "error" => $error,
    "response" => $response
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
