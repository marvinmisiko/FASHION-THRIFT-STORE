<?php 
	$db_name = 'mysql:host=localhost;dbname=coffee';
	$db_user = 'root';
	$db_password ='';

	try {
		$conn = new PDO($db_name,$db_user,$db_password);
	} catch (PDOException $e) {
		echo 'failed to connect'.$e->getMessage();
	}


	
	function unique_id(){
		$chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charLength = strlen($chars);
		$randomString = '';
		for ($i=0; $i < 20 ; $i++) { 
			$randomString.=$chars[mt_rand(0, $charLength - 1)];
		}
		return $randomString;
	}


	function unique_orderId($length = 9) {
		$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$randomString = '';
	
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
	
		return '#' . $randomString;
	}


?>