<?php
include 'components/connection.php';
session_start();
if (isset($_SESSION['user_id'])) {
	$user_id = $_SESSION['user_id'];
} else {
	$user_id = '';
}

if (isset($_POST['logout'])) {
	session_destroy();
	header("location: login.php");
}
try {
	if (isset($_POST['submit-message'])) {
		$id = unique_id();
		$user_id = $_SESSION['user_id'];
		$name = filter_var($_POST['name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		$subject = filter_var($_POST['subject'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		$email = filter_var($_POST['email'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		$message = filter_var($_POST['message'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		$post_message = $conn->prepare("INSERT INTO `message`(id,user_id,subject,name,email,message) VALUES(?,?,?,?,?,?)");
		$post_message->execute([$id, $user_id, $subject, $name, $email, $message]);
		if ($post_message) {
			$success_msg[] = 'message sent';
		} else {
			$warning_msg[] = 'somthing went wrong';
		}
	}
} catch (PDOException $e) {
	echo "error" . $e->getMessage();
}
?>
<style type="text/css">
	<?php include 'style.css'; ?>
</style>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Store - Contact Us</title>
</head>

<body>
	<?php include 'components/header.php'; ?>
	<div class="main">
		<div class="banner">
			<h1>contact us</h1>
		</div>
		<div class="title2">
			<a href="index.php">home </a><span>/ contact us</span>
		</div>
		
		<div class="form-container">
			<form method="post" action="">
				<div class="title">
					<h1 style="color: black; font-size : 40px; padding: 10px;">Store</h1>
					<h1>leave a message</h1>
					<p>We Love feedback, Please reach us incase of anything</p>
				</div>
				<div class="input-field">
					<p>your name <sup>*</sup></p>
					<input type="text" name="name">
				</div>
				<div class="input-field">
					<p>subject <sup>*</sup></p>
					<input type="text" name="subject">
				</div>
				<div class="input-field">
					<p>your email <sup>*</sup></p>
					<input type="email" name="email">
				</div>
				<div class="input-field">
					<p>your number <sup>*</sup></p>
					<input type="text" name="number">
				</div>
				<div class="input-field">
					<p>your message <sup>*</sup></p>
					<textarea name="message"></textarea>
				</div>
				<button type="submit" name="submit-message" class="btn">send message</button>
			</form>

		</div>

		<?php include 'components/footer.php'; ?>
	</div>
	<script src="script.js"></script>
	<?php include 'components/alert.php'; ?>
</body>

</html>