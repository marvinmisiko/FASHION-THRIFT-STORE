<header class="header">
	<div class="flex">
		<a href="index.php" class="logo"><strong style="font-size: 40px; text-decoration: none; color: black;">STORE</strong></a>
		<nav class="navbar">
			<a href="index.php">home</a>
			<a href="view_products.php">products</a>
			<a href="order.php">orders</a>
			<a href="contact.php">contact us</a>
		</nav>
		<div class="icons">
			<img src="icons/user.png" id="user-btn" style="height: 25px; margin-left: 5px;" />
			<?php
			$count_wishlist_items = $conn->prepare("SELECT * FROM `wishlist` WHERE user_id = ?");
			$count_wishlist_items->execute([$user_id]);
			$total_wishlist_items = $count_wishlist_items->rowCount();
			?>
			<a href="wishlist.php" class="cart-btn"><img src="icons/heart.png" style="height: 25px; margin-left: 5px;" /><sup style="font-size: 8px"><?= $total_wishlist_items ?></sup></a>
			<?php
			$count_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
			$count_cart_items->execute([$user_id]);
			$total_cart_items = $count_cart_items->rowCount();
			?>
			<a href="cart.php" class="cart-btn"><img src="icons/cart.png" style="height: 25px; margin-left: 5px; " /><sup style="font-size: 8px"><?= $total_cart_items ?></sup></a>
			<img src="icons/list.png" id="menu-btn" style="height: 25px" />
		</div>
		<div class="user-box">
			<p>username : <span><?php echo $_SESSION['user_name'] ?? "Log In to set"; ?></span></p>
			<p>Email : <span><?php echo $_SESSION['user_email'] ?? "Log In to set"; ?></span></p>

			<?php if (empty($user_id)) {  ?>
				<a href="login.php" class="btn" style="color: white;">login</a>
				<a href="register.php" class="btn" style="color: white;">register</a>
			<?php } ?>

			<?php if (!empty($user_id)) {  ?>
				<form method="post">
					<button type="submit" name="logout" class="logout-btn">log out</button>
				</form>
			<?php } ?>

		</div>
	</div>
</header>