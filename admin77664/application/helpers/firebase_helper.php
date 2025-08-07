<?php

// function sendNotification($title, $body, $tokens, $imageUrl = '') {
//     // Check if tokens are provided
//     if (empty($tokens)) {
//         echo 'Error: Tokens are missing!';
//         die;
//     }

//     // Path to your Firebase service account file
//     $serviceAccountFile = base_url() . 'assets/admin/uwin-firebase-credentials.json';

//     // Get the access token using the service account
//     $accessToken = getAccessToken($serviceAccountFile);
//     if (empty($accessToken)) {
//         echo 'Error: Failed to get access token!';
//         die;
//     }

//     // Set FCM API URL
//     $url = 'https://fcm.googleapis.com/v1/projects/uwinn-7cfa3/messages:send';

//     // Define the headers, including the Authorization Bearer token
//     $header = [
//         'Authorization: Bearer ' . $accessToken,
//         'Content-Type: application/json'
//     ];

//     // Define the notification payload
//     $notification = [
//         'title' => $title,
//         'body' => $body,
//         'image' => $imageUrl
//         // 'image' => base_url($imageUrl) // Add the image URL directly in the notification block
//     ];

    

//     // Define the message payload
//     $message = [
//         'message' => [
//             'token' => '', // Placeholder, will be set in the loop
//             'notification' => $notification,
//             'android' => [
//                 'priority' => 'high',
//                 'notification' => [
//                     'sound' => 'default' // Ensure sound is set for Android
//                 ]
//             ]
//         ]
//     ];

//     // Split tokens into chunks of 500 (FCM allows 500 tokens per request)
//     $chunkedTokens = array_chunk($tokens, 500);

//     // Loop through each chunk of tokens and send notifications
//     foreach ($chunkedTokens as $tokenBatch) {
//         $multiCurl = curl_multi_init();
//         $curlHandles = [];

//         foreach ($tokenBatch as $token) {
//             $message['message']['token'] = $token; // Set the token for this message

//             // Initialize cURL for each token
//             $ch = curl_init();
//             curl_setopt($ch, CURLOPT_URL, $url);
//             curl_setopt($ch, CURLOPT_POST, true);
//             curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
//             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//             curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification
//             curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));

//             // Add the cURL handle to the multi-cURL handler
//             curl_multi_add_handle($multiCurl, $ch);
//             $curlHandles[] = $ch;
//         }

//         // Execute all cURL requests in parallel
//         $running = null;
//         do {
//             curl_multi_exec($multiCurl, $running);
//             curl_multi_select($multiCurl);
//         } while ($running > 0);

//         // Process responses for each token
//         foreach ($curlHandles as $ch) {
//             $response = curl_multi_getcontent($ch);
//             if (curl_errno($ch)) {
//                 echo 'Error: ' . curl_error($ch);
//             } else {
//                 $responseObj = json_decode($response, true);

//                 // Debug the response
//                 echo 'Response: ' . json_encode($responseObj) . PHP_EOL;

//                 // Check for any errors in the response
//                 if (isset($responseObj['error'])) {
//                     echo 'Error in response: ' . $responseObj['error'] . PHP_EOL;
//                 }
//             }
//             curl_multi_remove_handle($multiCurl, $ch);
//             curl_close($ch);
//         }

//         // Close the multi-cURL handler
//         curl_multi_close($multiCurl);

//         // Optional: Add a delay between batches to avoid rate limiting
//         sleep(1); // Sleep for 1 second between batches (adjust as needed)
//     }

//     return true;
// }
function sendNotification($title, $body, $tokens, $imageUrl = '', $notification_id) {
    if (empty($tokens) || !is_array($tokens)) {
        echo 'Error: Token missing or invalid!';
        return false;
    }

    // Extract the first token (only one will be used)
    $token = $tokens[0];

    $serviceAccountFile = FCPATH . 'assets/admin/uwin-firebase-credentials.json';
    $accessToken = getAccessToken($serviceAccountFile);

    if (empty($accessToken)) {
        echo 'Error: Failed to get access token!';
        return false;
    }

    $url = 'https://fcm.googleapis.com/v1/projects/uwinn-7cfa3/messages:send';
    $headers = [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json'
    ];

    // Notification display content
    $notification = [
        'title' => $title,
        'body' => $body,
        'image' => $imageUrl
    ];

    // Android-specific customization
    $android = [
        'notification' => $notification,
        'priority' => 'high'
    ];

    // Custom data payload (for internal app handling)
    $dataPayload = [
        'notification_id' => (string)$notification_id,
        'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
    ];

    // Final request body
    $request = [
        'message' => [
            'token' => $token,
            'notification' => $notification,
            'android' => $android,
            'data' => $dataPayload
        ]
    ];

    // Send notification using cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For dev only
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request));

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Curl error: ' . curl_error($ch);
        curl_close($ch);
        return false;
    }

    curl_close($ch);
    $responseObj = json_decode($response, true);

    if (isset($responseObj['error'])) {
        echo 'FCM Error: ' . $response;
        return false;
    }

    return true;
}

function sendNotificationToTopic($title, $body, $imageUrl = '')
{
    // Absolute path to Firebase credentials
    $serviceAccountFile = FCPATH . 'assets/admin/uwin-firebase-credentials.json';

    // Get access token
    $accessToken = getAccessToken($serviceAccountFile);
    if (empty($accessToken)) {
        echo 'Error: Failed to get access token!';
        return false;
    }

    // FCM HTTP v1 endpoint
    $url = 'https://fcm.googleapis.com/v1/projects/uwinn-7cfa3/messages:send';

    // Headers
    $header = [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json'
    ];

    // Payload (no notification_id)
    $payload = [
        'message' => [
            'topic' => 'Brodcast_all',
            'notification' => [
                'title' => $title,
                'body' => $body,
                'image' => $imageUrl ?: null
            ],
            'android' => [
                'notification' => [
                    'sound' => 'default',
                    'image' => $imageUrl ?: null
                ]
            ],
            'apns' => [
                'payload' => [
                    'aps' => [
                        'sound' => 'default'
                    ]
                ]
            ]
        ]
    ];

    $payload = json_encode($payload);

    // cURL request
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        return true;
    } else {
        log_message('error', 'Push Error [' . $httpCode . ']: ' . $response);
        return false;
    }
}
function getAccessToken($serviceAccountFile) {
    $googleApiUrl = "https://oauth2.googleapis.com/token";
    $jsonData = file_get_contents($serviceAccountFile);

    $serviceAccount = json_decode($jsonData, true);

    $postData = [
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion' => generateJWT($serviceAccount)
    ];

    // Initialize cURL to send the request to get the access token
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $googleApiUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    $response = curl_exec($ch);
    curl_close($ch);

    // Decode the response and extract the access token
    $responseObj = json_decode($response);

    return $responseObj->access_token;
}

function generateJWT($serviceAccount) {
    $issuedAt = time();
    $expirationTime = $issuedAt + 3600; // Access token expires in 1 hour

    // Create the payload for the JWT
    $payload = [
        'iss' => $serviceAccount['client_email'],
        'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
        'aud' => 'https://oauth2.googleapis.com/token',
        'exp' => $expirationTime,
        'iat' => $issuedAt
    ];

    // Encode the payload into JSON format
    $header = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
    $payload = base64_encode(json_encode($payload));

    // Sign the message with the private key
    $privateKey = $serviceAccount['private_key'];

    // Prepare the message to sign
    $message = "$header.$payload";

    // Sign the message with the private key
    $signature = '';
    openssl_sign($message, $signature, $privateKey, OPENSSL_ALGO_SHA256);
    $signature = base64_encode($signature);

    // Return the JWT token
    return "$header.$payload.$signature";
}
?>
