<?php
require_once realpath(__DIR__ . "/vendor/autoload.php");
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

require 'mysql_con.php';

$verify_token = $_ENV['ACCESS_TOKEN'];

$access_token = $_ENV['ACCESS_TOKEN'];
$phone_number_id = "391470320715202";

// Log incoming GET request for debugging
file_put_contents('debug.log', date('Y-m-d H:i:s') . " - GET: " . print_r($_GET, true) . PHP_EOL, FILE_APPEND);

// Handle verification request
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['hub_mode']) && $_GET['hub_mode'] == 'subscribe' && isset($_GET['hub_verify_token']) && $_GET['hub_verify_token'] == $verify_token) {
    echo $_GET['hub_challenge'];
    exit;
} else {
    file_put_contents('debug.log', date('Y-m-d H:i:s') . " - Verification: Token did not match" . PHP_EOL, FILE_APPEND);
}

// Handle webhook events
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!empty($data['entry'][0]['changes'])) {
    foreach ($data['entry'][0]['changes'] as $change) {
        if (isset($change['value']['messages'])) {
            foreach ($change['value']['messages'] as $message) {
                if (isset($message['from']) && isset($message['text']['body'])) {
                    $from = $message['from'];
                    $text = $message['text']['body'];

                    // Log the specific fields
                    file_put_contents('messages.log', date('Y-m-d H:i:s') . " Message: $from $text" . PHP_EOL, FILE_APPEND);
                    
                    $query = "SELECT * FROM msgs WHERE contact='$from'";
                    $result = mysqli_query($connection, $query);

                    if (mysqli_num_rows($result) == 0) {
                        $sql = "INSERT INTO msgs (contact, msg_content) VALUES ('$from', '$text')";
                        if (mysqli_query($connection, $sql)) {
                            $body="Welcome to HelpDeskChatbot. To ask your query kindly enter your message in this format: 1 Your Query here";

                         sendFirstReturnMessage($from,$body, $access_token, $phone_number_id);
                            echo "New record created successfully";
                        } else {
                            file_put_contents('db_results.log', date('Y-m-d H:i:s') . " Error occurred: " . $connection->error . PHP_EOL, FILE_APPEND);
                        }
                    } elseif (mysqli_num_rows($result) > 0) {
                        $firstCharacter = substr($text, 0, 1);
                        if($firstCharacter==1){
                            sendSecondReturnMessage($from, $text, $access_token, $phone_number_id);
                            $sql = "INSERT INTO msgs (contact, msg_content,query) VALUES ('$from', '$text',1)";
                            if (mysqli_query($connection, $sql)) {
                                   echo "New record created successfully";
                               } else {
                                   file_put_contents('db_results.log', date('Y-m-d H:i:s') . " Error occurred: " . $connection->error . PHP_EOL, FILE_APPEND);
                               }
                        }else{
                            $body="Format error. To ask your query kindly enter your message in this format: 1 Your Query here";

                            sendFirstReturnMessage($from,$body, $access_token, $phone_number_id);

                        }

                       
                    }

                }
            }
        }
    }
}

function sendFirstReturnMessage($to,$body, $access_token, $phone_number_id) {
    $url = "https://graph.facebook.com/v20.0/$phone_number_id/messages";
    $data = [
        "messaging_product" => "whatsapp",
        "recipient_type" => "individual",
        "to" => $to,
        "type" => "text", // Specify the type of message as "text"
        "text" => [
            "body" => $body
        ]
    ];
    

    $jsonData = json_encode($data);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $access_token
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    file_put_contents('messages.log', date('Y-m-d H:i:s') . " - Response: $response - HTTP Code: $httpcode" . PHP_EOL, FILE_APPEND);
}


function sendSecondReturnMessage($to, $text, $access_token, $phone_number_id) {
    $url = "https://graph.facebook.com/v20.0/$phone_number_id/messages";
    $data = [
        "messaging_product" => "whatsapp",
        "recipient_type" => "individual",
        "to" => $to,
        "type" => "template",
        "template" => [
            "name" => "second_reply",
            "language" => [
                "code" => "en"
            ],
            "components" => [
                [
                    "type" => "body"
                
                    ]
                ]
            ]
                ];

    $jsonData = json_encode($data);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $access_token
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    file_put_contents('messages.log', date('Y-m-d H:i:s') . " - Response: $response - HTTP Code: $httpcode" . PHP_EOL, FILE_APPEND);
}

http_response_code(200);
echo 'Webhook received';
?>
