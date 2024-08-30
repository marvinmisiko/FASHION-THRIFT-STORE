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


//adding products in wishlist
if (isset($_POST['add_to_wishlist'])) {
	if (empty($user_id)) {
		$warning_msg[] = 'please login to add products to wishlist';
	} else {
		$id = unique_id();
		$product_id = $_POST['product_id'];

		$varify_wishlist = $conn->prepare("SELECT * FROM `wishlist` WHERE user_id = ? AND product_id = ?");
		$varify_wishlist->execute([$user_id, $product_id]);

		$cart_num = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ? AND product_id = ?");
		$cart_num->execute([$user_id, $product_id]);

		if ($varify_wishlist->rowCount() > 0) {
			$warning_msg[] = 'product already exist in your wishlist';
		} else if ($cart_num->rowCount() > 0) {
			$warning_msg[] = 'product already exist in your cart';
		} else {
			$select_price = $conn->prepare("SELECT * FROM `products` WHERE id = ? LIMIT 1");
			$select_price->execute([$product_id]);
			$fetch_price = $select_price->fetch(PDO::FETCH_ASSOC);

			$insert_wishlist = $conn->prepare("INSERT INTO `wishlist`(id, user_id,product_id,price) VALUES(?,?,?,?)");
			$insert_wishlist->execute([$id, $user_id, $product_id, $fetch_price['price']]);
			$success_msg[] = 'product added to wishlist successfully';
		}
	}
}


//adding products in cart
if (isset($_POST['add_to_cart'])) {
	if (empty($user_id)) {
		$warning_msg[] = 'please login to add products to cart';
	} else {
		$id = unique_id();
		$product_id = $_POST['product_id'];

		$selectedQuantity =  1;
		$availableQuantity = (int)$_POST['available_quantity'];


		$varify_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ? AND product_id = ?");
		$varify_cart->execute([$user_id, $product_id]);

		$max_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
		$max_cart_items->execute([$user_id]);

		if ($varify_cart->rowCount() > 0) {
			$warning_msg[] = 'product already exist in your cart';
		} else if ($max_cart_items->rowCount() > 20) {
			$warning_msg[] = 'cart is full';
		} else {
			$select_price = $conn->prepare("SELECT * FROM `products` WHERE id = ? LIMIT 1");
			$select_price->execute([$product_id]);
			$fetch_price = $select_price->fetch(PDO::FETCH_ASSOC);
			$insert_cart = $conn->prepare("INSERT INTO `cart`(id, user_id,product_id,price,qty) VALUES(?,?,?,?,?)");
			$insert_cart->execute([$id, $user_id, $product_id, $fetch_price['price'], $selectedQuantity]);
			$success_msg[] = 'product added to cart successfully';
		}
	}
}

//searching products
if (isset($_POST['search_btn'])) {
	$search = $_POST['search'];
	$search = filter_var($search, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	$select_search = $conn->prepare("SELECT * FROM `products` WHERE name LIKE ?");
	$select_search->execute(["%$search%"]);
	if ($select_search->rowCount() > 0) {
		$success_msg[] = 'search result';
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
<style type="text/css">
	<?php include 'style.css'; ?>
</style>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Store - Product Page</title>
</head>

<body>
	<?php include 'components/header.php'; ?>
	<div class="main">
		<div class="banner">
			<h1>store</h1>
		</div>
		<div class="title2">
			<a href="index.php">home </a><span>/ our store</span>
		</div>
		<section class="products">
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



			<div class="box-container">
				<?php
				if (isset($search)) {
					if ($select_search->rowCount() > 0) {
						while ($fetch_product = $select_search->fetch(PDO::FETCH_ASSOC)) {
				?>
							<form action="" method="post" class="box">
								<div class="image-container">
									<img src="admin_panel/image/<?= $fetch_product['image']; ?>" class="img">
								</div>
								<div class="button">
									<button type="submit" name="add_to_cart"><img src="icons/cart.png" style="height: 25px; margin-left: 5px; " /></button>
									<button type="submit" name="add_to_wishlist"><img src="icons/heart.png" style="height: 25px; margin-left: 5px;" /><sup style="font-size: 8px"></button>
									<a href="view_page.php?pid=<?php echo $fetch_product['id']; ?>"><img src="icons/show.png" style="height: 25px; margin-left: 5px;" /></a>
								</div>
								<h3 class="name"><?= $fetch_product['name']; ?></h3>
								<input type="hidden" name="product_id" value="<?= $fetch_product['id']; ?>">
								<div class="flex">
									<p class="price">Ksh. <?= number_format($fetch_products['price'], 2); ?></p>
									<!-- <input type="number" name="qty" required min="1" value="1" max="99" maxlength="2" class="qty"> -->
								</div>
								<a href="checkout.php?get_id=<?= $fetch_product['id']; ?>" class="btn">buy now</a>
							</form>
				<?php

						}
					} else {
						echo '<p class="empty">no products with ' . $search . ' </p>';
					}
				}

				?>
			</div>

			<?php
			if (isset($search)) {
			?>
				<h1 class="title">Continue Shopping</h1>
			<?php
			}
			?>


			<div class="box-container">
				<?php
				$select_products = $conn->prepare("SELECT * FROM `products` WHERE status = ? AND qty > 0 ORDER BY id DESC");
				$select_products->execute(['active']);
				if ($select_products->rowCount() > 0) {
					while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
				?>
						<form method="post">
							<div class="box product" data-category="<?= $fetch_products['category_id'] ?>">
								<div class="image-container">
									<img src="admin_panel/image/<?= $fetch_products['image']; ?>" class="img">
								</div>
								<input type="hidden" name="available_quantity" value="<?= $fetch_products['qty']; ?>">

								<div class="button">
									<button type="submit" name="add_to_cart"><img src="icons/cart.png" style="height: 25px; margin-left: 5px;" /></button>
									<button type="submit" name="add_to_wishlist"><img src="icons/heart.png" style="height: 25px; margin-left: 5px;" /><sup style="font-size: 8px"></sup></button>
									<a href="view_page.php?pid=<?= $fetch_products['id']; ?>"><img src="icons/show.png" style="height: 25px; margin-left: 5px;" /></a>
								</div>
								<h3 class="name"><?= $fetch_products['name']; ?></h3>
								<input type="hidden" name="product_id" value="<?= $fetch_products['id']; ?>">

								<div class="flex m-vert-12">
									<p id="product-price-<?= $fetch_products['id']; ?>" class="price" data-price="<?= $fetch_products['price']; ?>">Ksh. <?= number_format($fetch_products['price'], 2); ?></p>
									<span class="m-hor-20"></span>
								</div>
								<a href="checkout.php?get_id=<?= $fetch_products['id']; ?>" class="btn">buy now</a>
							</div>
						</form>
				<?php
					}
				} else {
					echo '<p class="empty">no products added yet!</p>';
				}
				?>
			</div>
		</section>
		<?php include 'components/footer.php'; ?>
	</div>
	<script src="script.js"></script>

	<script>
		const filterCategory = document.getElementById('filterCategory');

		// An event change listener to the select element
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

			const products = document.getElementsByClassName('product');

			// Loop through each product el
			for (const product of products) {
				// Get the data-category attrib val of the product el
				const productCategory = product.getAttribute('data-category');

				// If "All Categories" is selected or the product's category matches the selected category, show the product
				if (selectedCategory === 'all' || productCategory === selectedCategory) {
					product.parentNode.style.display = 'block';
				} else {
					product.parentNode.style.display = 'none';
				}
			}
		}
		filterProducts();
	</script>


	<?php include 'components/alert.php'; ?>
</body>

</html>