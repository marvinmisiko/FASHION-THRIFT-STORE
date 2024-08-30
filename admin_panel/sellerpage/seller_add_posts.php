<?php
include '../components/connection.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
	header('location: admin_login.php');
}


if (isset($_POST['publish-btn'])) {
	$id = unique_id();
	$title = $_POST['title'];
	$title = filter_var($title, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

	$price = $_POST['price'];
	$price = filter_var($price, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

	$quantity = $_POST['quantity'];
	$quantity = filter_var($quantity, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

	$content = $_POST['content'];
	$content = filter_var($content, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

	$category_id = $_POST['category_id'];
	$category_id = filter_var($category_id, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

	$status = 'active';

	$image = $_FILES['image']['name'];
	$image = filter_var($image, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	$image_size = $_FILES['image']['size'];
	$image_tmp_name = $_FILES['image']['tmp_name'];
	$image_folder = '../image/' . $image;

	$select_image = $conn->prepare("SELECT * FROM `products` WHERE image = ?");
	$select_image->execute([$image]);

	if (isset($image)) {
		if ($select_image->rowCount() > 0) {
			$warning_msg[] = 'image name repeated!';
		} elseif ($image_size > 2000000) {
			$warning_msg[] = 'image size too large!';
		} else {
			move_uploaded_file($image_tmp_name, $image_folder);
		}
	} else {
		$image = '';
	}
	if ($select_image->rowCount() > 0 and $image != '') {
		$success_msg[] = 'please rename your image';
	} else {
		$insert_post = $conn->prepare("INSERT INTO `products`(id, name, price, qty, image, product_detail, category_id, status) VALUES (?,?,?,?,?,?,?,?)");
		$insert_post->execute([$id, $title, $price, $quantity, $image, $content, $category_id, $status]);
		$success_msg[] = 'post published';
	}
}

//post adding in draft
if (isset($_POST['draft-btn'])) {
	$id = unique_id();
	$title = $_POST['title'];
	$title = filter_var($title, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

	$price = $_POST['price'];
	$price = filter_var($price, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

	$quantity = $_POST['quantity'];
	$quantity = filter_var($quantity, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

	$content = $_POST['content'];
	$content = filter_var($content, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

	$category_id = $_POST['category_id'];
	$category_id = filter_var($category_id, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

	$status = 'deactive';

	$image = $_FILES['image']['name'];
	$image = filter_var($image, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	$image_size = $_FILES['image']['size'];
	$image_tmp_name = $_FILES['image']['tmp_name'];
	$image_folder = '../image/' . $image;

	$select_image = $conn->prepare("SELECT * FROM `products` WHERE image = ?");
	$select_image->execute([$image]);


	if (isset($image)) {
		if ($select_image->rowCount() > 0) {
			$warning_msg[] = 'image name repeated!';
		} elseif ($image_size > 2000000) {
			$message[] = 'image size too large!';
		} else {
			move_uploaded_file($image_tmp_name, $image_folder);
		}
	} else {
		$image = '';
	}
	if ($select_image->rowCount() > 0 and $image != '') {
		$message[] = 'please rename your image';
	} else {
		$insert_post = $conn->prepare("INSERT INTO `products`(id, name, price, qty, image, product_detail, category_id, status) VALUES (?,?,?,?,?,?,?,?)");
		$insert_post->execute([$id, $title, $price, $quantity, $image, $content, $category_id, $status]);
		$message[] = 'post publish';
	}
}

// Fetch categories from the categories tbl
$fetch_categories = $conn->query("SELECT category_id, category_name FROM categories");
$categories = $fetch_categories->fetchAll(PDO::FETCH_ASSOC);
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
			<h1>add products</h1>
		</div>
		<div class="title2">
			<a href="index.php">dashboard </a><span>/ add products </span>
		</div>

		<h1 class="heading">add product</h1>
		<div class="form-container">
			<form action="" method="post" enctype="multipart/form-data">
				<div style="display: flex;">
					<div class="input-field" style="width: 60%; margin-right: 20px;">
						<label>product name <sup>*</sup></label>
						<input type="text" name="title"  placeholder="add post title">
						<small></small>
					</div>
					<div class="input-field">
						<label>Category <sup>*</sup></label>
						<select name="category_id" >
							<?php
							foreach ($categories as $category) {
								echo '<option value="' . $category['category_id'] . '">' . $category['category_name'] . '</option>';
							}
							?>
						</select>
						<small></small>
					</div>
				</div>
				<div class="input-field">
					<label>product quantity <sup>*</sup></label>
					<input type="number" name="quantity" value="1" min="1" max="9999" placeholder="add post quantity">
					<small></small>
				</div>
				<div class="input-field">
					<label>product price <sup>*</sup></label>
					<input type="number" name="price" min="0" placeholder="add post price">
					<small></small>
				</div>
				<div class="input-field">
					<label>product detail<sup>*</sup></label>
					<textarea name="content" placeholder="write your content.."></textarea>
					<small></small>
				</div>

				<div class="input-field">
					<label>product image <sup>*</sup></label>
					<input type="file" name="image" accept="image/jpg, image/jpeg, image/png, image/webp">
					<small></small>
				</div>
				<div class="flex-btn">
					<input type="submit" name="publish-btn" value="publish post" class="btn">
					<input type="submit" name="draft-btn" value="Save Draft" class="option-btn">
				</div>
			</form>
		</div>
		</section>
	</div>

	<script type="text/javascript" src="script.js"></script>
	<?php include '../components/alert.php'; ?>
	<script>
    const titleEl = document.querySelector('[name="title"]');
    const categoryEl = document.querySelector('[name="category_id"]');
    const quantityEl = document.querySelector('[name="quantity"]');
    const priceEl = document.querySelector('[name="price"]');
    const contentEl = document.querySelector('[name="content"]');
    const imageEl = document.querySelector('[name="image"]');
    const form = document.querySelector('form');

    const isRequired = value => Boolean(value);

    const isValidTitle = () => isRequired(titleEl.value.trim()) && /^[a-zA-Z0-9\s]{4,100}$/.test(titleEl.value.trim());
    const isValidCategory = () => isRequired(categoryEl.value);
    const isValidQuantity = () => isRequired(quantityEl.value.trim()) && /^[1-9]\d{0,3}$/.test(quantityEl.value.trim());
    const isValidPrice = () => isRequired(priceEl.value.trim()) && /^\d+(\.\d{1,2})?$/.test(priceEl.value.trim());
    const isValidContent = () => isRequired(contentEl.value.trim());
    const isValidImage = () => isRequired(imageEl.value);

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

    const validateForm = () => {
        let isValidForm = true;

        if (!isValidTitle()) {
            showError(titleEl, 'Product name must be alphanumeric and between 4 and 100 characters.');
            isValidForm = false;
        } else {
            showSuccess(titleEl);
        }

        if (!isValidCategory()) {
            showError(categoryEl, 'Category is required.');
            isValidForm = false;
        } else {
            showSuccess(categoryEl);
        }

        if (!isValidQuantity()) {
            showError(quantityEl, 'Quantity must be a number between 1 and 9999.');
            isValidForm = false;
        } else {
            showSuccess(quantityEl);
        }

        if (!isValidPrice()) {
            showError(priceEl, 'Price is required and must be a non-negative number with up to 2 decimal places.');
            isValidForm = false;
        } else {
            showSuccess(priceEl);
        }

        if (!isValidContent()) {
            showError(contentEl, 'Product detail is required.');
            isValidForm = false;
        } else {
            showSuccess(contentEl);
        }

        if (!isValidImage()) {
            showError(imageEl, 'Product image is required.');
            isValidForm = false;
        } else {
            showSuccess(imageEl);
        }

        return isValidForm;
    };

    form.addEventListener('submit', (e) => {
        if (!validateForm()) {
            e.preventDefault();
        }
    });
</script>

</body>

</html>