<?php
session_start();

// echo '<a href="index.php">Home<br /></a>';

include '../components/connection.php';

$orderNo = isset($_SESSION['orderNo']) ? $_SESSION['orderNo'] : '';

$content = file_get_contents('php://input'); //Receives the JSON Result from safaricom
$res = json_decode($content, true); //Convert the json to an array

$dataToLog = array(
    date("Y-m-d H:i:s"), //Date and time
    " MerchantRequestID: " . $res['Body']['stkCallback']['MerchantRequestID'],
    " CheckoutRequestID: " . $res['Body']['stkCallback']['CheckoutRequestID'],
    " ResultCode: " . $res['Body']['stkCallback']['ResultCode'],
    " ResultDesc: " . $res['Body']['stkCallback']['ResultDesc'],
);

$data = implode(" - ", $dataToLog);
$data .= PHP_EOL;
file_put_contents('transaction_log', $data, FILE_APPEND); //Logs the results to our log file


if ($res['Body']['stkCallback']['ResultCode'] == "0") {
    $sql = $conn->prepare("UPDATE orders SET status = 'Completed' WHERE order_no = ?");
    $rs = $sql->execute([$orderNo]);
    unset($_SESSION['orderNo']);
    

} else {
    $sql = $conn->prepare("UPDATE orders SET status = 'Cancelled' WHERE order_no = ?");
    $rs = $sql->execute([$orderNo]);
    unset($_SESSION['orderNo']);
}

if ($rs) {
    file_put_contents('error_log', "Records Inserted\n", FILE_APPEND);;
} else {
    file_put_contents('error_log', "Failed to insert Records\n", FILE_APPEND);
}
