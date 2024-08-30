<?php
include 'components/connection.php';
session_start();

if (isset($_SESSION['user_id'])) {
	$user_id = $_SESSION['user_id'];
} else {
	$user_id = '';
}

//register user
if (isset($_POST["register-btn"])) {
	$id = unique_id();
	$name = $_POST['name'];
	$name = filter_var($name, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	$email = $_POST['email'];
	$email = filter_var($email, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	$pass = sha1($_POST['pass']);
	$pass = filter_var($pass, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	$cpass = sha1($_POST['cpass']);
	$cpass = filter_var($cpass, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

	$select_user = $conn->prepare("SELECT * FROM `users` WHERE  email = ?");
	$select_user->execute([$email]);
	$row = $select_user->fetch(PDO::FETCH_ASSOC);

	if ($select_user->rowCount() > 0) {
		$warning_msg[] = 'email already exist';
	} else {
		if ($pass != $cpass) {
			$warning_msg[] = 'confirm your password';
		} else {
			$insert_user = $conn->prepare("INSERT INTO `users`(id,name,email,password) VALUES(?,?,?,?)");
			$insert_user->execute([$id, $name, $email, $pass]);
			header('location: index.php');
			$select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND password = ?");
			$select_user->execute([$email, $pass]);
			$row = $select_user->fetch(PDO::FETCH_ASSOC);
			if ($select_user->rowCount() > 0) {
				$_SESSION['user_id'] = $row['id'];
				$_SESSION['user_name'] = $row['name'];
				$_SESSION['user_email'] = $row['email'];
			}
		}
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
	<title>Store - Register</title>
</head>

<body>
	<div class="main-container">
		<section class="form-container">
			<div class="title">
				<h1 style="color: black; font-size : 40px; padding: 10px;">Welcome to Store</h1>
				<h1>register now</h1>
				<p>create your account and enjoy
				</p>
			</div>
			<form action="" method="post" enctype="multipart/form-data">
				<h3>register now</h3>
				<div class="input-field">
					<label style="display: flex; justify-content: flex-start;">User name <sup>*</sup></label>
					<input type="text" name="name" placeholder="Enter your username" style="display: flex; justify-content: flex-start;">
					<small style="display: flex; justify-content: flex-start;"></small>
				</div>
				<div class="input-field">
					<label style="display: flex; justify-content: flex-start;">email <sup>*</sup></label>
					<input type="text" name="email" placeholder="Enter your email" style="display: flex; justify-content: flex-start;">
					<small style="display: flex; justify-content: flex-start;"></small>
				</div>
				<div class="input-field">
					<label style="display: flex; justify-content: flex-start;">password <sup>*</sup></label>
					<input type="password" name="pass" placeholder="Enter your password" style="display: flex; justify-content: flex-start;">
					<small style="display: flex; justify-content: flex-start;"></small>
				</div>
				<div class="input-field">
					<label style="display: flex; justify-content: flex-start;">confirm password <sup>*</sup></label>
					<input type="password" name="cpass" placeholder="Enter your password" style="display: flex; justify-content: flex-start;">
					<small style="display: flex; justify-content: flex-start;"></small>
				</div>
				<div class="input-field">
					<input type="submit" name="register-btn" value="register now" class="btn">
				</div>
				<p>already have an account ? <a href="login.php">login now</a></p>
			</form>

		</section>
	</div>
	<?php include 'components/alert.php'; ?>

	<script>
		// get references to the form inputs &  form element
		const nameEl = document.querySelector('[name="name"]'),
			emailEl = document.querySelector('[name="email"]'),
			passwordEl = document.querySelector('[name="pass"]'),
			confirmPasswordEl = document.querySelector('[name="cpass"]'),
			btn = document.querySelector('[name="register-btn"]');

		// define a function to check if a value is
		const isRequired = value => Boolean(value);

		const showSuccess = (input) => {
			const formField = input.parentElement;
			formField.classList.remove('error');
			formField.classList.add('success');
			const error = formField.querySelector('small');
			error.textContent = '';
		};
		// define a function to show an error message for an input element
		const showError = (input, message) => {
			const formField = input.parentElement;
			formField.classList.remove('success');
			formField.classList.add('error');
			const error = formField.querySelector('small');
			error.textContent = message;
		};

		const isValid = (input, pattern, message) => {
			const value = input.value.trim();
			if (!isRequired(value)) {
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

		// define a function to check the username, email and password inputs
		const checkName = () => isValid(nameEl, /^[a-zA-Z\s]+$/, 'Name should only contain letters and be between 4 and 22 characters in length.');
		const checkEmail = () => isValid(emailEl, /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/, 'Email is not valid.');
		const checkPassword = () => isValid(passwordEl, /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*]).{8,}$/, 'Password must have at least 8 characters that include at least 1 lowercase character, 1 uppercase character, 1 number, and 1 special character.');

		const checkPasswordMatch = () => {
			const password = passwordEl.value.trim();
			const confirmPassword = confirmPasswordEl.value.trim();
			if (!isRequired(confirmPassword)) {
				showError(confirmPasswordEl, 'Confirm password cannot be blank.');
				return false;
			} else if (password !== confirmPassword) {
				showError(confirmPasswordEl, 'Passwords do not match.');
				return false;
			} else {
				showSuccess(confirmPasswordEl);
				return true;
			}
		};



		// Event listener for the form submit button
		btn.addEventListener('click', (e) => {

			// Check each field for errors and if any fields have errors, prevent form submission
			let isValidForm = true;

			if (!checkName()) {
				isValidForm = false;
			}

			if (!checkEmail()) {
				isValidForm = false;
			}

			if (!checkPassword()) {
				isValidForm = false;
			}

			if (!checkPasswordMatch()) {
				isValidForm = false;
			}

			// If all fields are valid, submit the form
			if (isValidForm) {} else {
				// Prevent form submission
				e.preventDefault();
			}
		});
	</script>
</body>

</html>