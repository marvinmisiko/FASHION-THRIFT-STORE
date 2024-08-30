<?php
include '../components/connection.php';

session_start();

if (isset($_POST['admin-login-btn'])) {

	$name = $_POST['name'];
	$name = filter_var($name, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

	$pass = sha1($_POST['password']);
	$pass = filter_var($pass, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

	$select_admin = $conn->prepare("SELECT * FROM `admin` WHERE name = ? AND password = ?");
	$select_admin->execute([$name, $pass]);

	if ($select_admin->rowCount() > 0) {
		$fetch_admin_id = $select_admin->fetch(PDO::FETCH_ASSOC);
		$_SESSION['admin_id'] = $fetch_admin_id['id'];
		header('location:dashboard.php');
	} else {
		$message[] = 'incorrect username or password';
	}
}
?>
<style>
	<?php include 'admin_style.css'; ?>
</style>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Store - Admin Login</title>
</head>

<body style="padding-left: 0 !important;">
	<?php
	if (isset($message)) {
		foreach ($message as $message) {
			echo '
					<div class="message">
						<span>' . $message . '</span>
						<i class="bx bx-x" onclick="this.parentElement.remove();"></i>
					</div>
				';
		}
	}
	?>
	<div class="main-container">
		<section class="form-container" id="admin_login">
			<form action="" method="post">
				<div class="title">
					<h1 style="color: black; font-size : 40px; padding: 10px;">Welcome to Shop</h1>
					<h1>Admin login</h1>
				</div>
				<div class="input-field">
					<label>Username <sup>*</sup></label><br>
					<input type="text" name="name" placeholder="Enter your username" >
					<small></small>
				</div>
				<div class="input-field">
					<label>password <sup>*</sup></label><br>
					<input type="password" name="password" placeholder="Enter your password">
					<small></small>
				</div>
				<input type="submit" name="admin-login-btn" value="login now" class="btn">
			</form>
		</section>
	</div>

	<script>
		const nameEl = document.querySelector('[name="name"]'),
			passwordEl = document.querySelector('[name="password"]'),
			btn = document.querySelector('[name="admin-login-btn"]');

		// define a function to check if a value is required
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

		const checkName = () => isValid(nameEl, /^[a-zA-Z\s]+$/, 'Username should only contain letters and be between 4 and 22 characters in length.');
		const checkPassword = () => isValid(passwordEl, /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*]).{8,}$/, 'Password must have at least 8 characters that include at least 1 lowercase character, 1 uppercase character, 1 number, and 1 special character.');

		// Event listener for the form submit button
		btn.addEventListener('click', (e) => {

			// Check each field for errors and if any fields have errors, prevent form submission
			let isValidForm = true;

			if (!checkName()) {
				isValidForm = false;
			}

			if (!checkPassword()) {
				isValidForm = false;
			}

			// If all fields are valid, submit the form
			if (!isValidForm) {
				e.preventDefault();
			}
		});
	</script>
</body>

</html>