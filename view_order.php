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
if (isset($_GET['get_id'])) {
	$get_id = $_GET['get_id'];
} else {
	$get_id = '';
	header('location:order.php');
}
if (isset($_POST['cancle'])) {
	$update_order = $conn->prepare("UPDATE `orders` SET status = ? WHERE id=?");
	$update_order->execute(['Cancelled', $get_id]);
	header('location:order.php');
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
	<title>Store - Order Detail Page</title>
</head>

<body>
	<?php include 'components/header.php'; ?>
	<div class="main">
		<div class="banner">
			<h1>order detail</h1>
		</div>
		<div class="title2">
			<a href="index.php">home </a><span>/ order detail</span>
		</div>
		<section class="order-detail">
			<div class="title">
				<h1 style="color: black; font-size : 40px; padding: 10px;">STORE</h1>
				<h1>order detail</h1>
				<p> You can see your order detail here
				</p>
			</div>
			<div class="box-container">
				<?php
				$grand_total = 0;
				$select_orders = $conn->prepare("SELECT * FROM `orders` WHERE id=? LIMIT 1");
				$select_orders->execute([$get_id]);
				if ($select_orders->rowCount() > 0) {
					while ($fetch_order = $select_orders->fetch(PDO::FETCH_ASSOC)) {
						$select_product = $conn->prepare("SELECT * FROM `products` WHERE id=? LIMIT 1");
						$select_product->execute([$fetch_order['product_id']]);
						if ($select_product->rowCount() > 0) {
							while ($fetch_product = $select_product->fetch(PDO::FETCH_ASSOC)) {
								$sub_total = ($fetch_order['price'] * $fetch_order['qty']);
								$grand_total += $sub_total;

				?>
								<div class="box">
									<div class="col">
										<p class="title"><img src="icons/calendar.png" style="margin-top: 10px; margin-right: 5px; height: 17px;" /><?= $fetch_order['date']; ?></p>
										<img src="admin_panel/image/<?= $fetch_product['image']; ?>" class="image">
										<p class="price"><?= $fetch_product['price']; ?> x <?= $fetch_order['qty']; ?></p>
										<h3 class="name"><?= $fetch_product['name']; ?></h3>
										<p class="grand-total">Total amount payable : <span>KES <?= $grand_total; ?></span></p>
									</div>
									<div class="col">
										<p class="title">billing address</p>
										<p class="user"><img src="icons/user_order.png" style="margin-top: 15px; margin-right: 5px; height: 20px;" /><?= $fetch_order['name']; ?></p>
										<p class="user"><img src="icons/phone.png" style="margin-top: 15px; margin-right: 5px; height: 20px;" /><?= $fetch_order['number']; ?></p>
										<p class="user"><img src="icons/message.png" style="margin-top: 15px; margin-right: 5px; height: 20px;" /><?= $fetch_order['email']; ?></p>
										<p class="user"><img src="icons/add.png" style="margin-top: 15px; margin-right: 5px; height: 20px;" /><?= $fetch_order['address']; ?></p>
										<p class="title">status</p>
										<p class="status" style="color:<?php if ($fetch_order['status'] == 'Completed') {
																			echo 'green';
																		} elseif ($fetch_order['status'] == 'Cancelled') {
																			echo 'red';
																		} else {
																			echo 'orange';
																		} ?>"><?= $fetch_order['status'] ?></p>
										<?php if ($fetch_order['status'] == 'Cancelled') { ?>
											<a href="checkout.php?get_id=<?= $fetch_product['id']; ?>" class="btn">order again</a>
										<?php } else if ($fetch_order['status'] == 'In Progress') { ?>
											<form method="post">
												<button type="submit" name="cancle" class="btn" onclick="return confirm('do you want to cancel this order')">cancel order</button>
											</form>
										<?php } ?>
									</div>
								</div>
				<?php
							}
						} else {
							echo '<p class="empty">product not found</p>';
						}
					}
				} else {
					echo '<p class="empty">no order found</p>';
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