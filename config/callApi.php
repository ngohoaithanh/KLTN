<?php
function callApi($url, $method = 'GET', $data = null) {
    $curl = curl_init();

    $headers = [
        'Content-Type: application/x-www-form-urlencoded'
    ];

    if ($method === 'POST') {
        curl_setopt($curl, CURLOPT_POST, true);
        if ($data) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        }
    } elseif ($method === 'GET' && $data) {
        $url .= '?' . http_build_query($data);
    }

    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_TIMEOUT => 30,
    ]);

    $response = curl_exec($curl);
    $error = curl_error($curl);
    curl_close($curl);

    if ($error) {
        return ['success' => false, 'error' => $error];
    }

    $result = json_decode($response, true);
    return $result ?: ['success' => false, 'error' => 'Invalid JSON response'];
}
