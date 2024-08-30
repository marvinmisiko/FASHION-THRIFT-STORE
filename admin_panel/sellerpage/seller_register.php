<?php
include '../components/connection.php';

if (isset($_POST["admin-register-btn"])) {
	$id = unique_id();
	$name = $_POST['name'];
	$name = filter_var($name, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	$email = $_POST['email'];
	$email = filter_var($email, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	$pass = sha1($_POST['password']);
	$pass = filter_var($pass, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	$cpass = sha1($_POST['cpassword']);
	$cpass = filter_var($cpass, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

	$image = $_FILES['image']['name'];
	$image = filter_var($image, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	$image_tmp_name = $_FILES['image']['tmp_name'];
	$image_folder = '../image/' . $image;

	$select_admin = $conn->prepare("SELECT * FROM `admin` WHERE name = ?");
	$select_admin->execute([$name]);

	if ($select_admin->rowCount() > 0) {
		$warning_msg[] = 'username already exists!';
	} else {
		if ($pass != $cpass) {
			$warning_msg[] = 'confirm passowrd not matched!';
		} else {
			$insert_admin = $conn->prepare("INSERT INTO `admin`(id, name, email, password,profile) VALUES(?,?,?,?,?)");
			$insert_admin->execute([$id, $name, $email, $cpass, $image]);
			move_uploaded_file($image_tmp_name, $image_folder);
			$success_msg[] = 'new admin registered!';
		}
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
	<title>Admin Register Page</title>
</head>

<body style="padding-left: 0 !important;">

	<div class="main-container">


		<section>

			<div class="form-container" id="admin_login">
				<form action="" method="post" enctype="multipart/form-data">
					<h3>register now</h3>
					<div class="input-field">
						<label>User name <sup>*</sup></label>
						<input type="text" name="name" placeholder="Enter your username">
						<small></small>
					</div>
					<div class="input-field">
						<label>email <sup>*</sup></label>
						<input type="text" name="email" placeholder="Enter your email">
						<small></small>
					</div>
					<div class="input-field">
						<label>password <sup>*</sup></label>
						<input type="password" name="password" placeholder="Enter your password">
						<small></small>
					</div>
					<div class="input-field">
						<label>confirm password <sup>*</sup></label>
						<input type="password" name="cpassword" placeholder="Enter your password">
						<small></small>
					</div>
					<div class="input-field">
						<label>upload profile <sup>*</sup></label>
						<input type="file" name="image" accept="image/jpg, image/jpeg, image/png, image/webp">
						<small></small>
					</div>
					<input type="submit" name="admin-register-btn" value="register now" class="btn">
					<p>already have an account ? <a href="admin_login.php">login now</a></p>
				</form>
			</div>
		</section>
	</div>

	<!-- custom js link  -->
	<script type="text/javascript" src="script.js"></script>

	<?php include '../components/alert.php'; ?>
	<!--  JS validations -->
	<script>
		// get references to the form inputs &  form element
		const profilePicEl = document.querySelector('[name="image"]'),
			nameEl = document.querySelector('[name="name"]'),
			emailEl = document.querySelector('[name="email"]'),
			passwordEl = document.querySelector('[name="password"]'),
			confirmPasswordEl = document.querySelector('[name="cpassword"]'),
			btn = document.querySelector('[name="admin-register-btn"]');

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

		const checkProfilePic = () => {
			const allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
			const profilePicValue = profilePicEl.value.trim();

			if (!isRequired(profilePicValue)) {
				showError(profilePicEl, 'No image chosen!');
				return false;
			}

			// Get the file extension
			const fileExtension = profilePicValue.split('.').pop().toLowerCase();

			// Check if the file extension is allowed
			if (!allowedExtensions.includes(fileExtension)) {
				showError(profilePicEl, 'Invalid file format. Allowed formats are JPG, JPEG, PNG, and WebP.');
				return false;
			}

			// Check the file size (max 5 MB)
			const fileSizeInBytes = profilePicEl.files[0].size;
			const maxSizeInBytes = 5 * 1024 * 1024; // 5 MB
			if (fileSizeInBytes > maxSizeInBytes) {
				showError(profilePicEl, 'File size exceeds the maximum limit (5 MB).');
				return false;
			}

			// All checks passed, show success message
			showSuccess(profilePicEl);
			return true;
		};


		// Event listener for the form submit button
		btn.addEventListener('click', (e) => {

			// Check each field for errors and if any fields have errors, prevent form submission
			let isValidForm = true;

			if (!checkProfilePic()) {
				isValidForm = false;
			}

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
			if (!isValidForm) {
				e.preventDefault();
			}
		});
	</script>
</body>

</html>