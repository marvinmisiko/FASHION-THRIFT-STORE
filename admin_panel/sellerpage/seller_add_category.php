<?php
include '../components/connection.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location: admin_login.php');
}

if (isset($_POST['add-cat-btn'])) {
    $categoryName = $_POST['catname'];
    $categoryName = filter_var($categoryName, FILTER_SANITIZE_FULL_SPECIAL_CHARS);


    if (!empty($categoryName)) {

        // Check if category already exists in db by its name
        $check_category = $conn->prepare("SELECT category_id FROM `categories` WHERE category_name = ?");
        $check_category->execute([$categoryName]);

        if ($check_category->rowCount() > 0) {
            $warning_msg[] = 'Category already exists.';
        } else {

            $category_id = unique_id();

            $insert_category = $conn->prepare("INSERT INTO `categories` (category_id, category_name) VALUES (?, ?)");
            $insert_category->execute([$category_id, $categoryName]);

            $success_msg[] = 'Category added successfully.';
        }
    } else {
        $warning_msg[] = 'Please enter a category name.';
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
    <title>admin dashboard</title>
</head>

<body>

    <?php include '../components/admin_header.php'; ?>
    <div class="main">
        <div class="banner">
            <h1>add category</h1>
        </div>
        <div class="title2">
            <a href="dashboard.php">dashboard </a><span>/ add category </span>
        </div>

        <h1 class="heading">add category</h1>
        <div class="form-container">
            <form action="" method="post" enctype="multipart/form-data">
                <div class="input-field">
                    <label>Category name <sup>*</sup></label>
                    <input type="text" name="catname" placeholder="category title e.g Sportwear etc">
                    <small></small>
                </div>
                <div class="flex-btn">
                    <input type="submit" name="add-cat-btn" value="Save" class="btn">
                </div>
            </form>
        </div>
        </section>
    </div>

    <script type="text/javascript" src="script.js"></script>
    <?php include '../components/alert.php'; ?>

    <script>
		const nameEl = document.querySelector('[name="catname"]'),
			btn = document.querySelector('[name="add-cat-btn"]');

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
					showError(input, 'Category field cannot be blank');
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

			// Event listener for the form submit button
			btn.addEventListener('click', (e) => {

				// Check each field for errors and if any fields have errors, prevent form submission
				let isValidForm = true;

				if (!checkName()) {
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