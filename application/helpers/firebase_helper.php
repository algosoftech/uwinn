<?php

// function sendNotification($title, $body, $tokens, $imageUrl = '',$notification_id=0) {
//     try{

//         // Check if tokens are provided
        
//         if (empty($tokens)) {
//             echo 'Error: Tokens are missing!';
//             die;
//         }
        
//     // Path to your Firebase service account file
//     $serviceAccountFile = 'https://u-winn.com/assets/uwin-firebase-credentials.json';
   
//     // Get the access token using the service account
//     $accessToken = getAccessToken($serviceAccountFile);
//     file_put_contents("log.txt", "Access token  :$serviceAccountFile " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
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
//         'body' => $body
//     ];
    
//     // Define the Android-specific fields (sound, image URL)
//     $android = [
//         'notification' => $notification,
//         'sound' => 'default',  // Android sound file or 'default'
//         'image' => $imageUrl // URL of the image (optional)
//     ];
    
//     // Define the message payload
//     $message = [
//         'message' => [
//             'token' => $tokens, // The target device tokens
//             'android' => $android, // Android-specific fields
//             ]
//         ];
        
//         // Split tokens into chunks of 500 (FCM allows 500 tokens per request)
//         $chunkedTokens = array_chunk($tokens, 500);
        
//         // Loop through each chunk of tokens and send notifications
//         $totalChunks = count($chunkedTokens);
//         for ($i = 0; $i < $totalChunks; $i++) {
//             // Process each chunk with multi-cURL
//             $multiCurl = curl_multi_init();
//             $curlHandles = [];
            
//             foreach ($chunkedTokens[$i] as $token) {
//                 // Payload structure for a single token
//                 // $request = [
//                 //     'message' => [
//                 //         'notification' => $notification,
//                 //         'token' => $token
//                 //         ]
//                 //     ];
//                 $request = [
//                     'message' => [
//                         'token' => $token,
//                         'notification' => [
//                             'title' => $title,
//                             'body' => $body
//                         ],
//                         'data' => [
//                             'notification_id' => (string)$notification_id  // Must be string
//                         ]
//                     ]
//                 ];   
//                     // Initialize cURL for each token
//                     $ch = curl_init();
//                     curl_setopt($ch, CURLOPT_URL, $url);
//                     curl_setopt($ch, CURLOPT_POST, true);
//                     curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
//                     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//                     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  // Disable SSL verification
//                     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request));
                    
//                     // Add the cURL handle to the multi-cURL handler
//                     curl_multi_add_handle($multiCurl, $ch);
//                     $curlHandles[] = $ch;
//                 }
                
//                 // Execute all cURL requests in parallel
//                 $running = null;
//                 do {
//                     curl_multi_exec($multiCurl, $running);
//                     curl_multi_select($multiCurl);
//                 } while ($running > 0);
                
//                 // Process responses for each chunk
//                 foreach ($curlHandles as $ch) {
//                     $response = curl_multi_getcontent($ch);
//                     if (curl_errno($ch)) {
//                         echo 'Error: ' . curl_error($ch);
//                     } else {
//                         $responseObj = json_decode($response, true);
//                         // Check for any failed responses and handle retries if needed
//                         if (isset($responseObj['error'])) {
//                             // Retry logic (optional: retry failed requests)
//                             return  false;
//                         }
                        
                        
//                     }
//                     curl_multi_remove_handle($multiCurl, $ch);
//                     curl_close($ch);
//                 }
                
//                 // Close the multi-cURL handler
//                 curl_multi_close($multiCurl);
                
//                 // Optional: Add a delay between batches to avoid rate limiting
//                 if ($i < $totalChunks - 1) {
//                     sleep(2);  // Sleep for 2 seconds between batches (adjust as needed)
//                 }
//             }
//             return $responseObj;
//         }catch(Exception $e){
//             file_put_contents("log.txt", "$e " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
//         }
    
// }

// function getAccessToken($serviceAccountFile) {
//     $googleApiUrl = "https://oauth2.googleapis.com/token";
//     $jsonData = file_get_contents($serviceAccountFile);

//     $serviceAccount = json_decode($jsonData, true);

//     $postData = [
//         'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
//         'assertion' => generateJWT($serviceAccount)
//     ];

//     // Initialize cURL to send the request to get the access token
//     $ch = curl_init();
//     curl_setopt($ch, CURLOPT_URL, $googleApiUrl);
//     curl_setopt($ch, CURLOPT_POST, true);
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//     curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
//     $response = curl_exec($ch);
//     curl_close($ch);

//     // Decode the response and extract the access token
//     $responseObj = json_decode($response);

//     return $responseObj->access_token;
// }

function sendNotification($title, $body, $tokens, $imageUrl = '',$notification_id=0) {
    // Check if tokens are provided
    if (empty($tokens) || !is_array($tokens)) {
        echo 'Error: Tokens are missing or invalid!';
        return false;
    }

    // Get the first token from array (even if it has one)
    $token = reset($tokens); // or $tokens[0];

    // Path to your Firebase service account file
    $serviceAccountFile = 'https://u-winn.com/assets/uwin-firebase-credentials.json';

    // Get the access token (only once)
    $accessToken = getAccessToken($serviceAccountFile);
    if (empty($accessToken)) {
        echo 'Error: Failed to get access token!';
        return false;
    }

    // FCM v1 endpoint
    $url = 'https://fcm.googleapis.com/v1/projects/uwinn-7cfa3/messages:send';

    // Headers
    $headers = [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json',
        'Connection: keep-alive',
    ];

    // Notification body
    $request = [
        'message' => [
            'token' => $token,
            'notification' => [
                'title' => $title,
                'body' => $body,
                'image' => $imageUrl // Optional image
            ],
            'data' => [
                'notification_id' => (string)$notification_id  // Must be string
            ],
            'android' => [
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                    'image' => $imageUrl,
                    'sound' => 'default'
                ]
            ]
        ]
    ];
    

    // cURL to send single notification
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Optional, disable in prod only for testing

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'cURL Error: ' . curl_error($ch);
        curl_close($ch);
        return false;
    }

    curl_close($ch);

    // Optional: Decode and log response
    $result = json_decode($response, true);
    if (isset($result['error'])) {
        echo 'FCM Error: ' . $result['error']['message'];
        return false;
    }

    return true;
}
function getAccessToken($serviceAccountFile) {
    // ✅ Load CI instance manually in helper
    $CI =& get_instance();

    // ✅ Load session library if not loaded
    if (!isset($CI->session)) {
        $CI->load->library('session');
    }

    // 1. Check session token cache
    $token        = $CI->session->userdata('fcm_token');
    $tokenExpiry  = $CI->session->userdata('fcm_token_expiry');

    if ($token && $tokenExpiry && time() < $tokenExpiry) {
        return $token; // ✅ Use cached token
    }

    // 2. Generate new token
    $googleApiUrl = "https://oauth2.googleapis.com/token";
    $jsonData = file_get_contents($serviceAccountFile);
    $serviceAccount = json_decode($jsonData, true);

    $postData = [
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion'  => generateJWT($serviceAccount) // ✅ You must already have this function
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $googleApiUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    $response = curl_exec($ch);
    curl_close($ch);

    $responseObj = json_decode($response);

    if (!isset($responseObj->access_token)) {
        return null;
    }

    // 3. Save token + expiry to session
    $CI->session->set_userdata('fcm_token', $responseObj->access_token);
    $CI->session->set_userdata('fcm_token_expiry', time() + 3500); // 58 minutes

    return $responseObj->access_token;
}
function generateJWT($serviceAccount) {
    $issuedAt = time();
    $expirationTime = $issuedAt + 3600;  // Access token expires in 1 hour

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
