<?php

define("TOKEN", "5061768214:AAHfbQdYrl8mkEnY17ac7SZq_73EgDSpfAs");
define("CHAT_ID", 312501439);
define("METHOD_NAME", "sendMessage");

$content = file_get_contents('php://input');
$signedPayloadJSON = json_decode($content, true);

$signedPayloadValue = $signedPayloadJSON['signedPayload'];
$signedPayloadJWS_Payload = explode(".", $signedPayloadValue)[1];


$notificationTypeJSON = json_decode(base64_decode($signedPayloadJWS_Payload), true);
$notificationTypeDATA_JSON = $notificationTypeJSON['data'];

$resNotificationType = $notificationTypeJSON['notificationType'];
$resNotificationUUID = $notificationTypeJSON['notificationUUID'];
$resEnvironment = $notificationTypeDATA_JSON['environment'];


$signedTransactionInfoValue = $notificationTypeDATA_JSON['signedTransactionInfo'];
$signedTransactionInfoJWS_Payload = explode(".", $signedTransactionInfoValue)[1];
$signedTransactionInfoJSON = json_decode(base64_decode($signedTransactionInfoJWS_Payload), true);

$resProductId = $signedTransactionInfoJSON['productId'];
$date = $signedTransactionInfoJSON['purchaseDate'];
$resPurchaseDate = date("H:i:s d-m-Y", substr($date, 0, 10));
$date = $signedTransactionInfoJSON['expiresDate'];
$resExpiresDate = date("H:i:s d-m-Y", substr($date, 0, 10));


$dataStr = "productID: " . $resProductId . "\nnotificationType: " . $resNotificationType . "\npurchase: " . $resPurchaseDate . "\nexpires: " . $resExpiresDate . "\nenvironment: " . $resEnvironment . "\nUUID: " . $resNotificationUUID;

$send_data = [
    'text' => $dataStr
];

$send_data['chat_id'] = CHAT_ID;
$res = sendMessageTelegram(METHOD_NAME, $send_data);

function sendMessageTelegram($method, $data, $headers = [])
{
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_POST => 1,
        CURLOPT_HEADER => 0,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => 'https://api.telegram.org/bot' . TOKEN . '/' . $method,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => array_merge(array("Content-Type: application/json"), $headers)
    ]);
    print_r($curl);
    $result = curl_exec($curl);
    curl_close($curl);
    return (json_decode($result, 1) ? json_decode($result, 1) : $result);
}