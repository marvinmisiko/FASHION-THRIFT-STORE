<?php
include 'components/connection.php';
session_start();

if (isset($_SESSION['user_id'])) {
	$user_id = $_SESSION['user_id'];
} else {
	$user_id = '';
}

//register user
if (isset($_POST['login-btn'])) {

	$email = $_POST['email'];
	$email = filter_var($email, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	$pass = sha1($_POST['pass']);
	$pass = filter_var($pass, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

	$select_user = $conn->prepare("SELECT * FROM `users` WHERE  email = ? AND password = ?");
	$select_user->execute([$email, $pass]);
	$row = $select_user->fetch(PDO::FETCH_ASSOC);

	if ($select_user->rowCount() > 0) {
		$_SESSION['user_id'] = $row['id'];
		$_SESSION['user_name'] = $row['name'];
		$_SESSION['user_email'] = $row['email'];
		header('location: index.php');
	} else {
		$warning_msg[] = 'incorrect username or password';
	}
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
	<title>Store - Login</title>
</head>

<body>
	<div class="main-container">
		<section class="form-container">
			<div class="title">
				<h1 style="color: black; font-size : 40px; padding: 10px;">Welcome to the Store</h1>
				<h1>login now</h1>
				<p> You can login here</p>
			</div>
			<form action="" method="post">
				<div class="input-field">
					<label style="display: flex; justify-content: flex-start;">email <sup>*</sup></label>
					<input type="text" name="email" placeholder="Enter your email" style="display: flex; justify-content: flex-start;">
					<small></small>
				</div>
				<div class="input-field">
					<label style="display: flex; justify-content: flex-start;">password <sup>*</sup></label>
					<input type="password" name="pass" placeholder="Enter your password" style="display: flex; justify-content: flex-start;">
					<small></small>
				</div>
				<div class="input-field">
				<input type="submit" name="login-btn" value="login now" class="btn">
				</div>
				<p>do not have an account? <a href="register.php">register now</a></p>
			</form>
		</section>
	</div>
	<?php include 'components/alert.php'; ?>

	<script>
		const emailEl = document.querySelector('[name="email"]');
		const passwordEl = document.querySelector('[name="pass"]');
		const btn = document.querySelector('[name="login-btn"]');

		const is = value => Boolean(value);

		const showSuccess = (input) => {
			const formField = input.parentElement;
			formField.classList.remove('error');
			formField.classList.add('success');
			const error = formField.querySelector('small');
			error.textContent = '';
		};

		const showError = (input, message) => {
			const formField = input.parentElement;
			formField.classList.remove('success');
			formField.classList.add('error');
			const error = formField.querySelector('small');
			error.textContent = message;
		};

		const isValid = (input, pattern, message) => {
			const value = input.value.trim();
			if (!is(value)) {
				showError(input, `${input.name} cannot be blank.`);
				return false;
			} else if (!pattern.test(value)) {
				showError(input, message);
				return false;
			} else {
				showSuccess(input);
				return true;
			}
		};

		const checkEmail = () => isValid(emailEl, /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/, 'Email is not valid.');

		const checkPassword = () => isValid(passwordEl, /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*]).{8,}$/, 'Password must have at least 8 characters that include at least 1 lowercase character, 1 uppercase character, 1 number, and 1 special character.');

		btn.addEventListener('click', (e) => {
			let isValidForm = true;

			if (!checkEmail()) {
				isValidForm = false;
			}

			if (!checkPassword()) {
				isValidForm = false;
			}

			if (!isValidForm) {
				e.preventDefault();
			}
		});
	</script>
	
</body>


</html>