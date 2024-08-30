<?php 

// include 'components/connection.php';

// session_start();

$db_name = 'mysql:host=localhost;dbname=coffee';
	$db_user = 'root';
	$db_password ='';

	
		$conn = new PDO($db_name,$db_user,$db_password);
$warning_msg = [];

$config = array(
    "env"              => "sandbox",
    "BusinessShortCode"=> "174379",
    "key"              => "",
    "secret"           => "",
    "TransactionType"  => "CustomerPayBillOnline",
    "passkey"          => "",
    "CallBackURL"      => "https://a6c9-105-160-90-120.ngrok-free.app/shop/payments/callback.php", //When using localhost, Use Ngrok to forward the response to your Localhost
    "AccountReference" => "Mungz LTD",
    "TransactionDesc"  => "Payment",
);



if (isset($_POST['pay-btn'])) {

    $phone = $_POST['MSISDN'];
    $phone = "254{$phone}";
    $orderNo =$_POST['orderNo'];
    $amount = 1;
    // Uncomment the below when going live or wanting to pay 4 exact total 
    // $amount = $_SESSION['amount']; 

    $access_token = ($config['env']  == "live") ? "https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials" : "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials"; 
    $credentials = base64_encode($config['key'] . ':' . $config['secret']); 

    
    $ch = curl_init($access_token);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Basic " . $credentials]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    $response = curl_exec($ch);
    curl_close($ch);
    $result = json_decode($response); 
    $token = isset($result->{'access_token'}) ? $result->{'access_token'} : "N/A";

    $timestamp = date("YmdHis");
    $password  = base64_encode($config['BusinessShortCode'] . "" . $config['passkey'] ."". $timestamp);

    $curl_post_data = array( 
        "BusinessShortCode" => $config['BusinessShortCode'],
        "Password" => $password,
        "Timestamp" => $timestamp,
        "TransactionType" => $config['TransactionType'],
        "Amount" => $amount,
        "PartyA" => $phone,
        "PartyB" => $config['BusinessShortCode'],
        "PhoneNumber" => $phone,
        "CallBackURL" => $config['CallBackURL'],
        "AccountReference" => $config['AccountReference'],
        "TransactionDesc" => $config['TransactionDesc'],
    ); 

    $data_string = json_encode($curl_post_data);


    $endpoint = ($config['env'] == "live") ? "https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest" : "https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest"; 


    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer '.$token,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response     = curl_exec($ch);
    curl_close($ch);

    
    $result = json_decode(json_encode(json_decode($response)), true);

    // file_put_contents('tests_log',  $result, FILE_APPEND);
    

    if($result['ResponseCode'] === "0"){         //STK Push request successful

        $MerchantRequestID = $result['MerchantRequestID'];
        $CheckoutRequestID = $result['CheckoutRequestID'];

        $sql = "INSERT INTO `stk_transactions`(`order_no`, `amount`, `phone`, `CheckoutRequestID`, `MerchantRequestID`) VALUES ('".$orderNo."','".$amount."','".$phone."','".$CheckoutRequestID."','".$MerchantRequestID."');";
        
        $conn->exec($sql);

        header('location: order.php');

    }else{
        $warning_msg[] = $result['errorMessage'];
    }
}

?>

