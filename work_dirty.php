<?php

define("TOKEN", "5061768214:AAHfbQdYrl8mkEnY17ac7SZq_73EgDSpfAs");
define("CHAT_ID", 312501439);
define("METHOD_NAME", "sendMessage");

$content = file_get_contents('php://input');
$signedPayloadJSON = json_decode($content, true);

$signedPayloadValue = $signedPayloadJSON['signedPayload'];
$signedPayloadJWS_Payload = explode(".", $signedPayloadValue)[1];

file_put_contents('test.txt', 'content:' . $content . "<br>", FILE_APPEND | LOCK_EX);
file_put_contents('test.txt', 'signedPayloadJSON:' . $signedPayloadJSON . "<br>", FILE_APPEND | LOCK_EX);
file_put_contents('test.txt', 'signedPayloadValue:' . $signedPayloadValue . "<br>", FILE_APPEND | LOCK_EX);
file_put_contents('test.txt', 'signedPayloadJWS_Payload:' . $signedPayloadJWS_Payload . "<br>" . "<br>", FILE_APPEND | LOCK_EX);

/*
{
    "notificationType": "DID_RENEW",
    "notificationUUID": "66bb3574-7b5a-4af8-afdb-fae378c9c7e2",
    "data": {
        "bundleId": "com.castles.trip.OIIIO",
        "bundleVersion": "1.0",
        "environment": "Sandbox",
        "signedTransactionInfo": "str",
        "signedRenewalInfo": "str"
        },
    "version": "2.0"
}
*/
$notificationTypeJSON = json_decode(base64_decode($signedPayloadJWS_Payload), true);
$notificationTypeDATA_JSON = $notificationTypeJSON['data'];

$resNotificationType = $notificationTypeJSON['notificationType'];
$resNotificationUUID = $notificationTypeJSON['notificationUUID'];
$resEnvironment = $notificationTypeDATA_JSON['environment'];

file_put_contents('test.txt', 'notificationTypeJSON:' . $notificationTypeJSON . "<br>", FILE_APPEND | LOCK_EX);
file_put_contents('test.txt', 'notificationTypeDATA_JSON:' . $notificationTypeDATA_JSON . "<br>" . "<br>", FILE_APPEND | LOCK_EX);

/*
 {
    "transactionId":"1000000924306340",
    "originalTransactionId":"1000000818770533",
    "webOrderLineItemId":"1000000069228834",
    "bundleId":"com.castles.trip.OIIIO",
    "productId":"com.castles.trip.OIIIO.sub1m3trialSale",
    "subscriptionGroupIdentifier":"20796466",
    "purchaseDate":1638691486000,
    "originalPurchaseDate":1622299280000,
    "expiresDate":1638691666000,
    "quantity":1,
    "type":"Auto-Renewable Subscription",
    "inAppOwnershipType":"PURCHASED",
    "signedDate":1638691495651
}
 */
$signedTransactionInfoValue = $notificationTypeDATA_JSON['signedTransactionInfo'];
$signedTransactionInfoJWS_Payload = explode(".", $signedTransactionInfoValue)[1];
$signedTransactionInfoJSON = json_decode(base64_decode($signedTransactionInfoJWS_Payload), true);

$resProductId = $signedTransactionInfoJSON['productId'];
$date = $signedTransactionInfoJSON['purchaseDate'];
$resPurchaseDate = date("H:i:s d-m-Y", substr($date, 0, 10));
$date = $signedTransactionInfoJSON['expiresDate'];
$resExpiresDate = date("H:i:s d-m-Y", substr($date, 0, 10));

file_put_contents('test.txt', 'signedTransactionInfoValue:' . $signedTransactionInfoValue . "<br>", FILE_APPEND | LOCK_EX);
file_put_contents('test.txt', 'signedTransactionInfoJWS_Payload:' . $signedTransactionInfoJWS_Payload . "<br>", FILE_APPEND | LOCK_EX);
file_put_contents('test.txt', 'signedTransactionInfoJSON:' . $signedTransactionInfoJSON . "<br>" . "<br>" . "<br>", FILE_APPEND | LOCK_EX);

/*
{
    "originalTransactionId":"1000000818770533",
    "autoRenewProductId":"com.castles.trip.OIIIO.sub1m3trialSale",
    "productId":"com.castles.trip.OIIIO.sub1m3trialSale",
    "autoRenewStatus":1,
    "signedDate":1638691495631
}
 */
//$signedRenewalInfoValue = $notificationTypeDATA_JSON['signedRenewalInfo'];
//$signedRenewalInfoJWS_Payload = explode(".", $signedRenewalInfoValue)[1];
//$signedRenewalInfoJSON = json_encode(base64_decode($signedRenewalInfoJWS_Payload), true);


$dataStr = 'notificationType: ' . $resNotificationType . '\n UUID: ' . $resNotificationUUID . '\n environment: ' . $resEnvironment . '\n productID: ' . $resProductId . '\n purchase: ' . $resPurchaseDate . '\n expires: ' . $resExpiresDate;

$send_data = [
    'text' => $dataStr
];

$send_data['chat_id'] = CHAT_ID;
$res = sendMessageTelegram(METHOD_NAME, $send_data);
print_r($res);

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