<?php

include '../components/connection.php';
session_start();
$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
	header('location: admin_login.php');
}


//delete products from database
//delete order
if (isset($_POST['delete_order'])) {
	$delete_id = $_POST['order_id'];
	$delete_id = filter_var($delete_id, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

	$verify_delete = $conn->prepare("SELECT * FROM `orders` WHERE id = ?");
	$verify_delete->execute([$delete_id]);

	if ($verify_delete->rowCount() > 0) {
		$delete_review = $conn->prepare("DELETE FROM `orders` WHERE id = ?");
		$delete_review->execute([$delete_id]);
		$success_msg[] = "Order Deleted";
	} else {
		$warning_msg[] = 'Order Already Deleted';
	}
}


if (isset($_POST['search_btn'])) {
	$search = $_POST['search'];
	$search = filter_var($search, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

	$search_query = $conn->prepare("SELECT * FROM `orders` WHERE status = ? AND name LIKE ?");
	$search_query->execute(['Cancelled', '%' . $search . '%']);
	if ($search_query->rowCount() > 0) {
		$success_msg[] = 'search result for ' . $search;
	} else {
		$warning_msg[] = 'No result found for ' . $search;
	}
}

?>
<style type="text/css">
	<?php
	include 'admin_style.css';

	?>
</style>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>admin pannel</title>
</head>

<body>

	<?php include '../components/admin_header.php'; ?>
	<div class="main">
		<div class="banner">
			<h1>total canceled orders</h1>
		</div>
		<div class="title2">
			<a href="dashboard.php">home </a><span>/ canceled orders</span>
		</div>
		<section class="order-container">
			<form method="post" class="flex-btn">
				<input type="text" name="search" placeholder="search by name" class="search">
				<input type="submit" name="search_btn" value="search" class="btn">
			</form>
			<div class="box-container">
				<?php
				if (isset($search)) {
					if ($search_query->rowCount() > 0) {
						while ($fetch_search = $search_query->fetch(PDO::FETCH_ASSOC)) {
							$select_products = $conn->prepare("SELECT * FROM `products` WHERE id=?");
							$select_products->execute([$fetch_search['product_id']]);
							if ($select_products->rowCount() > 0) {
								while ($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)) {
				?>
									<div class="box">
										<div class="status" style="color: <?php if ($fetch_search['status'] == 'in progress') {
																				echo 'limegreen';
																			} else {
																				echo "coral";
																			} ?>;"><?= $fetch_search['status'] ==  "in progress" ? "active" : $fetch_search['status'] ?></div>
										<div class="detail">
											<p>user name: <span><?= $fetch_search['name']; ?></span></p>
											<p>user id: <span><?php echo $fetch_search['user_id']; ?></span></p>
											<p>placed on: <span><?= $fetch_search['date']; ?></span></p>
											<p>number : <span><?php echo $fetch_search['number']; ?></span></p>
											<p>email : <span><?php echo $fetch_search['email']; ?></span></p>
											<p>total price : <span><?php echo $fetch_search['price']; ?></span></p>
											<p>method : <span><?php echo $fetch_search['method']; ?></span></p>
											<p>address : <span><?php echo $fetch_search['address']; ?></span></p>
											<p>Product name : <span><?php echo $fetch_product['name']; ?></span></p>

											<?php if ($fetch_product['image'] != '') { ?>
												<div class="image_container">
													<img src="../image/<?= $fetch_product['image'] ?>" class="image">
												</div>
											<?php } ?>
										</div>
										<div class="btn-container">
											<form method="post">
												<input type="hidden" name="order_id" value="<?php echo $fetch_orders['id']; ?>">
												<select name="update_payment">
													<option disabled selected><?php echo $fetch_product['status']; ?></option>
												</select>
												<div class="flex-btn">
													<input type="submit" name="delete_order" value="delete order" class="btn" onclick="return confirm('delete this review');">
												</div>
											</form>
										</div>
									</div>
				<?php
								}
							}
						}
					} else {
						echo '<h1 class="heading">No result found for ' . $search . '</h1>';
					}
				}

				?>
			</div>
			<h1 class="heading">total canceled orders</h1>
			<div class="box-container">
				<?php
				$select_orders = $conn->prepare("SELECT * FROM `orders` WHERE status = ?");
				$select_orders->execute(['Cancelled']);
				if ($select_orders->rowCount() > 0) {
					while ($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)) {
						$select_products = $conn->prepare("SELECT * FROM `products` WHERE id=?");
						$select_products->execute([$fetch_orders['product_id']]);
						if ($select_products->rowCount() > 0) {
							while ($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)) {

				?>
								<div class="box">
									<div class="status" style="color: <?php if ($fetch_orders['status'] == 'in progress') {
																			echo 'limegreen';
																		} else {
																			echo "coral";
																		} ?>;"><?= $fetch_orders['status'] ?></div>
									<div class="detail">
										<p>user name: <span><?= $fetch_orders['name']; ?></span></p>
										<p>user id: <span><?php echo $fetch_orders['user_id']; ?></span></p>
										<p>placed on: <span><?= $fetch_orders['date']; ?></span></p>
										<p>number : <span><?php echo $fetch_orders['number']; ?></span></p>
										<p>email : <span><?php echo $fetch_orders['email']; ?></span></p>
										<p>total price : <span><?php echo $fetch_orders['price']; ?></span></p>
										<p>method : <span><?php echo $fetch_orders['method']; ?></span></p>
										<p>address : <span><?php echo $fetch_orders['address']; ?></span></p>
										<p>Product name : <span><?php echo $fetch_product['name']; ?></span></p>
										<?php if ($fetch_product['image'] != '') { ?>
											<div class="image_container">
												<img src="../image/<?= $fetch_product['image'] ?>" class="image">
											</div>
										<?php } ?>
									</div>
									<form method="post">
										<input type="hidden" name="order_id" value="<?php echo $fetch_orders['id']; ?>">
										<select name="update_payment">
											<option disabled selected><?php echo $fetch_orders['status']; ?></option>
										</select>
										<div class="flex-btn">
											<!-- <input type="submit" name="update_order" value="update payment" class="btn"> -->
											<input type="submit" name="delete_order" value="delete order" class="btn" onclick="return confirm('delete this canceled order');">
										</div>
									</form>

								</div>
				<?php
							}
						}
					}
				} else {
					echo '
								<div class="empty">
									<p>no canceled order placed yet!</p>
								</div>
							';
				}
				?>
			</div>
		</section>
	</div>

	<script src="script.js"></script>
	<?php include '../components/alert.php'; ?>

</body>

</html>