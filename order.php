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

if (isset($_POST['search_btn'])) {
	$search = $_POST['search'];
	$search = filter_var($search, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	$search = "%" . $search . "%";
	$select_search = $conn->prepare("SELECT * FROM `orders` WHERE user_id = ? AND product_id IN (SELECT id FROM products WHERE name LIKE ?) ORDER BY date DESC");
	$select_search->execute([$user_id, $search]);
	if ($select_search->rowCount() > 0) {
		$success_msg[] = 'search result for ' . $_POST['search'];
	} else {
		$warning_msg[] = 'No result found for ' . $_POST['search'];
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
	<title>Store - Orders</title>
</head>

<body>
	<?php include 'components/header.php'; ?>
	<div class="main">
		<div class="banner">
			<h1>my order</h1>
		</div>
		<div class="title2">
			<a href="index.php">home </a><span>/ order</span>
		</div>
		<section class="orders">
			<div class="title">
				<h1 style="color: black; font-size : 40px; padding: 10px;">STORE</h1>
				<h1>my orders</h1>
				<p> You can see your orders here.
				</p>

			</div>
			<form method="post" class="flex-btn">
				<input type="text" name="search" placeholder="search by product" class="search" style="border-radius: 20px; margin-right: 10px">
				<input type="submit" name="search_btn" value="search" class="btn">
			</form>
			<div class="box-container">
				<?php
				if (isset($search)) {
					if ($select_search->rowCount() > 0) {
						while ($fetch_search = $select_search->fetch(PDO::FETCH_ASSOC)) {
							$select_products = $conn->prepare("SELECT * FROM `products` WHERE id=?");
							$select_products->execute([$fetch_search['product_id']]);
							if ($select_products->rowCount() > 0) {
								while ($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)) {
				?>
									<div class="box" <?php if ($fetch_product['status'] == 'canceled') {
															echo 'style="border:2px solid red; font-size: 30px"';
														} ?>>
										<a href="view_order.php?get_id=<?= $fetch_search['id']; ?>">
											<p class="date"><img src="icons/calendar.png" style="margin-right: 5px; height: 12px;" /><span><?= $fetch_search['date']; ?></span></p>
											<img src="admin_panel/image/<?= $fetch_product['image']; ?>" class="image">
											<div class="row">
												<h3 class="name"><?= $fetch_product['name']; ?></h3>
												<p class="price">Price : KES <?= $fetch_search['price']; ?> x <?= $fetch_search['qty']; ?></p>
												<p class="status" style="color:<?php if ($fetch_order['status'] == 'Completed') {
																				echo 'green';
																			} elseif ($fetch_order['status'] == 'Cancelled') {
																				echo 'red';
																			} else {
																				echo 'orange';
																			} ?>"><?= $fetch_order['status'] == "In Progress" ? "active" : $fetch_order['status'] ?></p>
											<p class="status" style="color:<?php if ($fetch_order['status'] == 'Completed') {
																				echo 'green';
																			} elseif ($fetch_order['status'] == 'Cancelled') {
																				echo 'red';
																			} else {
																				echo 'orange';
																			} ?>"><?= $fetch_order['status'] == "Completed" ? "Fully Paid" : $fetch_order['status'] ?></p>
											</div>
										</a>
									</div>
				<?php
								}
							}
						}
					} else {
						echo '<p class="empty">no order with ' . $search . '</p>';
					}
				}
				?>
			</div>

			<?php
			if (isset($search)) {
			?>
				<h1 class="title">Continue checking orders</h1>
			<?php
			}
			?>

			<div class="box-container">
				<?php
				$select_orders = $conn->prepare("SELECT * FROM `orders` WHERE user_id = ? ORDER BY date DESC");
				$select_orders->execute([$user_id]);
				if ($select_orders->rowCount() > 0) {
					while ($fetch_order = $select_orders->fetch(PDO::FETCH_ASSOC)) {
						$select_products = $conn->prepare("SELECT * FROM `products` WHERE id=?");
						$select_products->execute([$fetch_order['product_id']]);
						if ($select_products->rowCount() > 0) {
							while ($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)) {


				?>
								<div class="box" <?php if ($fetch_order['status'] == 'canceled') {
														echo 'style="border:2px solid red; font-size: 30px"';
													} ?>>
									<a href="view_order.php?get_id=<?= $fetch_order['id']; ?>">
										<p class="date"><i class="bi bi-calender-fill"></i><span><?= $fetch_order['date']; ?></span></p>
										<img src="admin_panel/image/<?= $fetch_product['image']; ?>" class="image">
										<div class="row">
											<h3 class="name"><?= $fetch_product['name']; ?></h3>
											<p class="price">Price : KES <?= $fetch_order['price']; ?> x <?= $fetch_order['qty']; ?></p>
											<p class="status" style="color:<?php if ($fetch_order['status'] == 'Completed') {
																				echo 'green';
																			} elseif ($fetch_order['status'] == 'Cancelled') {
																				echo 'red';
																			} else {
																				echo 'orange';
																			} ?>"><?= $fetch_order['status'] == "In Progress" ? "active" : $fetch_order['status'] ?></p>
											<p class="status" style="color:<?php if ($fetch_order['status'] == 'Completed') {
																				echo 'green';
																			} elseif ($fetch_order['status'] == 'Cancelled') {
																				echo 'red';
																			} else {
																				echo 'orange';
																			} ?>"><?= $fetch_order['status'] == "Completed" ? "Fully Paid" : $fetch_order['status'] ?></p>
										</div>
									</a>

								</div>
				<?php
							}
						}
					}
				} else {
					echo '<p class="empty">no order takes placed yet!</p>';
				}
				?>
			</div>

		</section>
		<?php include 'components/footer.php'; ?>
	</div>
	<script src="script.js"></script>

	<?php include 'components/alert.php'; ?>
</body>

</html>