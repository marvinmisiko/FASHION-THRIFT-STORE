<?php
include '../components/connection.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
	header('location: admin_login.php');
}

//delete post from database

if (isset($_POST['delete'])) {
	$p_id = $_POST['product_id'];
	$p_id = filter_var($p_id, FILTER_SANITIZE_FULL_SPECIAL_CHARS);


	$delete_post = $conn->prepare("DELETE FROM `products` WHERE id = ?");
	$delete_post->execute([$p_id]);

	$message[] = 'post deleted successfully';
}

//search post
if (isset($_POST['search_btn'])) {
	$search = $_POST['search'];
	$search = filter_var($search, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

	$search_post = $conn->prepare("SELECT * FROM `products` WHERE name LIKE ?");
	$search_post->execute(['%' . $search . '%']);

	if ($search_post->rowCount() > 0) {
		$message[] = 'search result';
	} else {
		$warning_msg[] = 'no result found';
	}
}

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
			<h1>all products</h1>
		</div>
		<div class="title2">
			<a href="dashboard.php">home </a><span>/ all products</span>
		</div>
		<section class="post-editor">


			<form method="post" class="flex-btn">
				<input type="text" name="search" placeholder="search by name" class="search" style="border-radius: 20px; margin-right: 20px; width: 60%">
				<input type="submit" name="search_btn" value="search" class="btn" style="width: 20%; margin-right: 50px">
				<div class="input-field">
					<label style="display: flex; justify-content: flex-end;">Filter by</label>
					<select id="filterCategory" required style="border-radius: 20px; margin-top: 5px; padding: 5px">
						<option value="all" selected>All Categories</option>
						<?php foreach ($categories as $category) : ?>
							<option value="<?= $category['category_id'] ?>"><?= getCategoryNameById($category['category_id']) ?></option>
						<?php endforeach; ?>
					</select>
				</div>

			</form>
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
			<div class="show-post">
				<div class="box-container">
					<?php
					if (isset($search)) {
						if ($search_post->rowCount() > 0) {
							while ($fetch_post = $search_post->fetch(PDO::FETCH_ASSOC)) {
					?>
								<form method="post" class="box">
									<input type="hidden" name="product_id" value="<?= $fetch_posts['id']; ?>">
									<?php if ($fetch_post['image'] != '') { ?>
										<img src="../image/<?= $fetch_post['image'] ?>" class="image">
									<?php } ?>
									<div class="status" style="color: <?php if ($fetch_posts['status'] == 'active') {
																			echo 'limegreen';
																		} else {
																			echo "coral";
																		} ?>;">
										<?= $fetch_post['status'] ?>
									</div>
									<div class="price">KES <?= $fetch_post['price'] ?>/-</div>
									<div class="title"><?= $fetch_post['name'] ?></div>
									<div class="flex-btn">
										<a href="edit_post.php?id=<?= $fetch_posts['id']; ?>" class="btn">edit</a>
										<button type="submit" name="delete" class="btn" onclick="return confirm('delete this post?')">delete</button>
										<a href="read_posts.php?post_id=<?= $fetch_posts['id']; ?>" class="btn">view post</a>
									</div>
								</form>
					<?php
							}
						} else {
							echo '
							<div class="empty">
								<p>no result found! <br><a href="add_posts.php" class="btn" style="margin-top: 10px;">add post</a></p>
							</div>
						';
						}
					}
					?>
				</div>
			</div>

			<h1 class="heading">your products</h1>
			<div class="show-post">

				<div class="box-container">
					<?php
					$select_posts = $conn->prepare("SELECT * FROM `products`");
					$select_posts->execute();
					if ($select_posts->rowCount() > 0) {
						while ($fetch_posts = $select_posts->fetch(PDO::FETCH_ASSOC)) {
					?>
							<div class="box product" data-category="<?= $fetch_posts['category_id'] ?>">

								<form method="post" class="box">
									<input type="hidden" name="product_id" value="<?= $fetch_posts['id']; ?>">
									<?php if ($fetch_posts['image'] != '') { ?>
										<img src="../image/<?= $fetch_posts['image'] ?>" class="image">
									<?php } ?>
									<div class="status" style="color: <?= ($fetch_posts['status'] == 'active') ? 'limegreen' : 'coral' ?>;"><?= $fetch_posts['status'] ?></div>

									<div class="price">Ksh. <?= number_format($fetch_posts['price'], 2); ?></div>
									<div class="title"><?= $fetch_posts['name'] ?></div>
									<div class="flex-btn">
										<a href="edit_post.php?id=<?= $fetch_posts['id']; ?>" class="btn edit-btn">edit</a>
										<button type="submit" name="delete" class="btn delete-btn" onclick="return confirm('delete this post?')">delete</button>
										<a href="read_posts.php?post_id=<?= $fetch_posts['id']; ?>" class="btn">view post</a>
									</div>
								</form>
							</div>
					<?php
						}
					} else {

						echo '
								<div class="empty">
									<p>no post added yet! <br><a href="add_posts.php" class="btn" style="margin-top: 1.5rem;">add post</a></p>
								</div>
							';
					}
					?>
				</div>

			</div>
		</section>
	</div>
	<script>
		const filterCategory = document.getElementById('filterCategory');

		//An event change listener to the select element
		filterCategory.addEventListener('change', filterProducts);

		/**
		 * Function to filter products based on the category chosen from a dropdown menu. 
		 * The change event of the select element with the id "filterCategory" is monitored by this function. 
		 * When a different category is chosen, only items that fall within that category are displayed, unless "All Categories" is chosen. 
		 * @returns 'void'
		 */

		function filterProducts() {
			// Get the selected category from the select element
			const selectedCategory = filterCategory.value;

			// Get all product elements with class 'product'
			const products = document.getElementsByClassName('product');

			// Loop through each product element
			for (const product of products) {
				// Get the data-category attribute value of the product element
				const productCategory = product.getAttribute('data-category');

				// If "All Categories" is selected or the product's category matches the selected category, show the product
				if (selectedCategory === 'all' || productCategory === selectedCategory) {
					product.style.display = 'block'; // Show the product
				} else {
					product.style.display = 'none'; // Hide the product
				}
			}
		}
		filterProducts();
	</script>
	<script type="text/javascript" src="script.js"></script>
</body>

</html>