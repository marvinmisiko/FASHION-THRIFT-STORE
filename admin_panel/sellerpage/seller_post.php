<?php
include '../components/connection.php';
session_start();

$admin_id = $_SESSION['admin_id'];


if (!isset($admin_id)) {
	header('location: admin_login.php');
}

//edit post
if (isset($_POST['save-btn'])) {
	$post_id = $_GET['id'];
	$title = $_POST['title'];
	$title = filter_var($title, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	$price = $_POST['price'];
	$price = filter_var($price, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	$quantity = $_POST['quantity'];
	$quantity = filter_var($quantity, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	$content = $_POST['content'];
	$content = filter_var($content, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

	$status = $_POST['status'];
	$status = filter_var($status, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

	$category_id = $_POST['category_id'];
	$category_id = filter_var($category_id, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

	$update_post = $conn->prepare("UPDATE `products` SET name = ?, price = ?, qty = ?, product_detail = ?, category_id = ?, status = ? WHERE id = ?");
	$update_post->execute([$title, $price, $quantity, $content, $category_id, $status, $post_id]);

	$success_msg[] = 'post updated';

	$old_image = $_POST['old_image'];
	$image = $_FILES['image']['name'];
	$image_size = $_FILES['image']['size'];
	$image_tmp_name = $_FILES['image']['tmp_name'];
	$image_folder = '../image/' . $image;

	$select_image = $conn->prepare("SELECT * FROM `products` WHERE image = ?");
	$select_image->execute([$image]);

	if (!empty($image)) {
		if ($image_size > 2000000) {
			$warning_msg[] = 'image size is too large';
		} elseif ($select_image->rowCount() > 0 and $image != '') {
			$warning_msg[] = 'please rename your image';
		} else {
			$update_image = $conn->prepare("UPDATE `products` SET image = ? WHERE id = ?");
			$update_image->execute([$image, $post_id]);
			move_uploaded_file($image_tmp_name, $image_folder);
			if ($old_image != $image and $old_image != '') {
				unlink('../image/' . $old_image);
			}
			$success_msg[] = 'image updated!';
		}
	}
}

//delete post
if (isset($_POST['delete-post-btn'])) {

	$post_id = $_POST['post_id'];
	$post_id = filter_var($post_id, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	$delete_image = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
	$delete_image->execute([$post_id]);
	$fetch_delete_image = $delete_image->fetch(PDO::FETCH_ASSOC);
	if ($fetch_delete_image['image'] != '') {
		unlink('../image/' . $fetch_delete_image['image']);
	}
	$delete_post = $conn->prepare("DELETE FROM `products` WHERE id = ?");
	$delete_post->execute([$post_id]);
	$delete_comments = $conn->prepare("DELETE FROM `comments` WHERE post_id = ?");
	$delete_comments->execute([$post_id]);
	$success_msg[] = 'post deleted successfully!';
}

//delete image 

if (isset($_POST['delete_image'])) {
	$empty_image = '';
	$post_id = $_POST['post_id'];
	$post_id = filter_var($post_id, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	$delete_image = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
	$delete_image->execute([$post_id]);
	$fetch_delete_image = $delete_image->fetch(PDO::FETCH_ASSOC);
	if ($fetch_delete_image['image'] != '') {
		unlink('../image/' . $fetch_delete_image['image']);
	}
	$unset_image = $conn->prepare("UPDATE `products` SET image = ? WHERE id=?");
	$unset_image->execute([$empty_image, $post_id]);
	$success_msg[] = 'image deleted successfully';
}

$status_options = array("active", "deactive");

$fetch_categories = $conn->query("SELECT category_id, category_name FROM categories");
$categories = $fetch_categories->fetchAll(PDO::FETCH_ASSOC);

function getCategoryNameById($category_id)
{
	global $conn;

	$stmt = $conn->prepare("SELECT category_name FROM categories WHERE category_id = ?");
	$stmt->execute([$category_id]);
	$result = $stmt->fetchColumn();
	return $result;
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
			<h1>edit post</h1>
		</div>
		<div class="title2">
			<a href="index.php">home </a><span>/ edit post</span>
		</div>
		<section class="post-editor">


			<h1 class="heading">edit post</h1>
			<?php
			$post_id = $_GET['id'];
			$select_posts = $conn->prepare("SELECT * FROM `products` WHERE id =?");
			$select_posts->execute([$post_id]);
			if ($select_posts->rowCount() > 0) {
				while ($fetch_posts = $select_posts->fetch(PDO::FETCH_ASSOC)) {
			?>
					<div class="form-container">
						<a href="view_posts.php" class="btn" style="width:19%; text-align: center; height: 3rem; margin-top: .7rem;">go back</a>
						<form action="" method="post" enctype="multipart/form-data">
							<input type="hidden" name="old_image" value="<?= $fetch_posts['image']; ?>">
							<input type="hidden" name="post_id" value="<?= $fetch_posts['id']; ?>">
							<div style="display: flex;">
								<div class="input-field" style="width: 50%; margin-right: 20px;">
									<label>product name<sup>*</sup></label>
									<input type="text" name="title" placeholder="add post title" value="<?= $fetch_posts['name']; ?>">
									<small></small>
								</div>
								<div class="input-field">
									<label>Category <sup>*</sup></label>
									<select name="category_id">
										<?php foreach ($categories as $category) : ?>
											<?php
											$selected = ($category['category_id'] === $fetch_posts['category_id']) ? 'selected' : '';
											?>
											<option value="<?= $category['category_id'] ?>" <?= $selected ?>>
												<?= getCategoryNameById($category['category_id']) ?>
											</option>
										<?php endforeach; ?>
									</select>
								</div>

							</div>
							<div class="input-field">
								<label>post status <sup>*</sup></label>
								<select name="status">
									<?php foreach ($status_options as $option) : ?>
										<option value="<?= $option ?>" <?= ($option === $fetch_posts['status']) ? 'selected' : '' ?>>
											<?= $option ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="input-field">
								<label>product quantity <sup>*</sup></label>
								<input type="number" name="quantity" placeholder="add post quantity" value="<?= $fetch_posts['qty']; ?>">
								<small></small>
							</div>
							<div class="input-field">
								<label>product price <sup>*</sup></label>
								<input type="number" name="price" value="<?= $fetch_posts['price']; ?>">
								<small></small>
							</div>
							<div class="input-field">
								<label>product detail <sup>*</sup></label>
								<textarea name="content" maxlength="10000" placeholder="write your content.."><?= $fetch_posts['product_detail']; ?></textarea>
								<small></small>
							</div>


							<div class="input-field">
								<label>post image <sup>*</sup></label>
								<input type="file" name="image" accept="image/jpg, image/jpeg, image/png, image/webp">
								<?php if ($fetch_posts['image'] != '') { ?>
									<img src="../image/<?= $fetch_posts['image']; ?>" class="image" name="img-prod">

									<button type="submit" name="delete_image" class="btn">delete image</button>

								<?php } ?>
								<small></small>
							</div>

							<div class="flex-btn">
								<button type="submit" name="save-btn" class="btn save-btn">save post</button>
								<span class="m-hor-12"></span>
								<button type="submit" class="btn delete-btn" name="delete-post-btn">delete post</button>
							</div>

						</form>

						<a href="view_posts.php" class="btn" style="margin-top: 24px;width:49%; text-align: center; height: 3rem; margin-top: .7rem;">go back</a>

					</div>

				<?php
				}
			} else {

				echo '
							<div class="empty">
								<p>no post found!</p>
							</div>
					';

				?>
				<div class="flex-btn">
					<a href="view_posts.php" class="option-btn">view post</a>
					<a href="add_posts.php" class="btn">add post</a>
				</div>
			<?php } ?>
		</section>
	</div>

	<script>
		const nameEl = document.querySelector('[name="title"]'),
			quantityEl = document.querySelector('[name="quantity"]'),
			priceEl = document.querySelector('[name="price"]'),
			contentEl = document.querySelector('[name="content"]'),
			imageEl = document.querySelector('[name="image"]'),
			imgProd = document.querySelector('[name="img-prod"]'),
			form = document.querySelector('form');

		// Function to check if a value is required
		const isRequired = value => Boolean(value.trim());

		const showError = (input, message) => {
			const formField = input.parentElement;
			formField.classList.remove('success');
			formField.classList.add('error');
			const error = formField.querySelector('small');
			error.textContent = message;
		};

		const showSuccess = (input) => {
			const formField = input.parentElement;
			formField.classList.remove('error');
			formField.classList.add('success');
			const error = formField.querySelector('small');
			error.textContent = '';
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

		const checkName = () => isValid(nameEl, /^[a-zA-Z\s\d\(\),.-]+$/, 'Enter a product name that contain alphanumeric characters.');
		const checkQuantity = () => isValid(quantityEl, /^[1-9]\d*$/, 'product quantity must be a positive integer');
		const checkPrice = () => isValid(priceEl, /^\d+(\.\d{1,2})?$/, 'Enter a valid product price.');
		const checkContent = () => isValid(contentEl, /^[\s\S]*$/, 'Enter a product description that contains alphanumeric characters.');
		const checkImage = () => imgProd.src !== '' || showError(imageEl, 'Choose an image');

		form.addEventListener('submit', (e) => {
			let isValidForm = true;

			if (!checkName() || !checkQuantity() || !checkPrice() || !checkContent() || !checkImage()) {
				isValidForm = false;
				e.preventDefault();
			}

			return isValidForm;
		});
	</script>
	<!-- custom js link  -->
	<script type="text/javascript" src="script.js"></script>


	<?php include '../components/alert.php'; ?>
</body>

</html>