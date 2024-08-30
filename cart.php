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
//update product in cart

if (isset($_POST['update_cart'])) {
	$cart_id = $_POST['cart_id'];
	$cart_id = filter_var($cart_id, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	$qty = $_POST['qty'];
	$qty = filter_var($qty, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

	$update_qty = $conn->prepare("UPDATE `cart` SET qty = ? WHERE id = ?");
	$update_qty->execute([$qty, $cart_id]);

	$success_msg[] = 'cart quantity updated successfully';
}


//delete item from wishlist
if (isset($_POST['delete_item'])) {
	$cart_id = $_POST['cart_id'];
	$cart_id = filter_var($cart_id, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

	$varify_delete_items = $conn->prepare("SELECT * FROM `cart` WHERE id =?");
	$varify_delete_items->execute([$cart_id]);

	if ($varify_delete_items->rowCount() > 0) {
		$delete_cart_id = $conn->prepare("DELETE FROM `cart` WHERE id = ?");
		$delete_cart_id->execute([$cart_id]);
		$success_msg[] = "cart item delete successfully";
	} else {
		$warning_msg[] = 'cart item already deleted';
	}
}

//empty cart
if (isset($_POST['empty_cart'])) {
	$varify_empty_item = $conn->prepare("SELECT * FROM `cart` WHERE user_id=?");
	$varify_empty_item->execute([$user_id]);

	if ($varify_empty_item->rowCount() > 0) {
		$delete_cart_id = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
		$delete_cart_id->execute([$user_id]);
		$success_msg[] = "empty successfully";
	} else {
		$warning_msg[] = 'cart item already deleted';
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
	<title>Store - My Cart</title>
</head>

<body>
	<?php include 'components/header.php'; ?>
	<div class="main">
		<div class="banner">
			<h1>my cart</h1>
		</div>
		<div class="title2">
			<a href="index.php">home </a><span>/ cart</span>
		</div>
		<section class="products">
			<h1 class="title">products added in cart</h1>
			<div class="box-container">
				<?php
				$grand_total = 0;
				$select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
				$select_cart->execute([$user_id]);
				if ($select_cart->rowCount() > 0) {
					while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) {
						$select_products = $conn->prepare("SELECT * FROM `products` WHERE id= ?");
						$select_products->execute([$fetch_cart['product_id']]);
						if ($select_products->rowCount() > 0) {
							$fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)
				?>
							<form method="post" action="" class="box">
								<input type="hidden" name="cart_id" value="<?= $fetch_cart['id']; ?>">
								<div class="image_container">
									<img src="admin_panel/image/<?= $fetch_products['image']; ?>" class="img">
								</div>
								<h3 class="name"><?= $fetch_products['name']; ?></h3>

								<div class="flex m-vert-12">
									<p id="product-price-<?= $fetch_products['id']; ?>" class="price" data-price="<?= $fetch_products['price']; ?>">Ksh. <?= number_format($fetch_products['price'], 2); ?></p>
									<span class="m-hor-20"></span>
									<div class="number-input">
										<button data-productid="<?= $fetch_products['id']; ?>" data-action="subtract" class="minus"></button>
										<input id="qty-input-<?= $fetch_products['id']; ?>" min="1" name="qty" value="1" type="number" max="<?= $fetch_products['qty']; ?>" class="qty" readonly>
										<button data-productid="<?= $fetch_products['id']; ?>" data-action="add" class="plus"></button>
									</div>
								</div>

								<button type="submit" name="delete_item" class="btn" onclick="return confirm('delete this item')">delete</button>
							</form>
				<?php
						} else {
							echo '<p class="empty">product was not found</p>';
						}
					}
				} else {
					echo '<p class="empty">no products added yet!</p>';
				}
				?>
			</div>


			<div class="cart-total">
				<p id="subtotal" class="sub-total">Subtotal <span>Ksh. -/-</span></p>

				<div class="button">
					<form method="post">
						<button type="submit" name="empty_cart" class="btn" onclick="return confirm('are you sure to empty your cart')" style="margin-right: 4rem;">empty cart</button>
					</form>
					<a href="#" class="btn checkout-btn">proceed to checkout</a>
					<a href="#" class="checkout-link" style="display: none;"></a>
				</div>
			</div>

		</section>
		<div class="m-vert-12"></div>
		<?php include 'components/footer.php'; ?>
	</div>
	<script src="script.js"></script>
	<script>
		// Update the total price of a single product based on quantity
		const updateTotalPrice = (productId) => {
			const productPriceEl = document.getElementById(`product-price-${productId}`);
			const qtyInput = document.getElementById(`qty-input-${productId}`);

			const productPrice = parseFloat(productPriceEl.getAttribute('data-price'));
			const quantity = parseInt(qtyInput.value);
			const totalPrice = productPrice * quantity;

			// Format the total price and update the displayed value
			const formattedTotalPrice = totalPrice.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
			productPriceEl.innerText = `Ksh. ${formattedTotalPrice}`;
		};

		// Update quantity and calculate total price
		const updateQuantity = (productId, action) => {
			const qtyInput = document.getElementById(`qty-input-${productId}`);
			const currentQuantity = parseInt(qtyInput.value);
			const maxQuantity = parseInt(qtyInput.getAttribute('max'));

			let newQuantity;
			if (action === 'add') {
				newQuantity = Math.min(currentQuantity + 1, maxQuantity);
			} else if (action === 'subtract') {
				newQuantity = Math.max(currentQuantity - 1, 1);
			}

			qtyInput.value = newQuantity;
			updateTotalPrice(productId);
		};

		// Update the subtotal of all items in the cart
		const updateSubtotal = () => {
			const cartItems = document.querySelectorAll('.box');
			let subtotal = 0;

			cartItems.forEach((item) => {
				const productId = item.querySelector('.plus').getAttribute('data-productid');
				const qtyInput = document.getElementById(`qty-input-${productId}`);
				const productPriceEl = document.getElementById(`product-price-${productId}`);

				const productPrice = parseFloat(productPriceEl.getAttribute('data-price'));
				const quantity = parseInt(qtyInput.value);
				const productTotalPrice = productPrice * quantity;

				// Update the displayed total price for the item
				const formattedProductTotalPrice = productTotalPrice.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
				productPriceEl.innerText = `Ksh. ${formattedProductTotalPrice}`;

				subtotal += productTotalPrice;
			});

			// Update the displayed subtotal
			const formattedSubtotal = subtotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
			const subtotalEl = document.getElementById('subtotal');
			subtotalEl.innerHTML = `Subtotal <span>Ksh. ${formattedSubtotal}/-</span>`;
		};

		// Initialize subtotal display
		updateSubtotal();

		// Attach event listeners to plus and minus buttons
		document.querySelectorAll('.plus, .minus').forEach((button) => {
			button.addEventListener('click', (event) => {
				event.preventDefault();
				const productId = button.getAttribute('data-productid');
				const action = button.getAttribute('data-action');
				updateQuantity(productId, action);
				updateSubtotal();
			});
		});

		// Generate query parameters for checkout
		const generateCartItemParams = () => {
			const cartItems = document.querySelectorAll('.box');
			const params = [];

			cartItems.forEach((item) => {
				const productId = item.querySelector('.plus').getAttribute('data-productid');
				const qtyInput = document.getElementById(`qty-input-${productId}`);
				const productPriceEl = document.getElementById(`product-price-${productId}`);

				const quantity = parseInt(qtyInput.value);
				const productPrice = parseFloat(productPriceEl.getAttribute('data-price'));

				// Build query parameter for each product
				params.push(`product_${productId}_qty=${quantity}&product_${productId}_price=${productPrice}`);
			});

			return params.join('&');
		};

		// Attach event listener to checkout button
		document.addEventListener('DOMContentLoaded', () => {
			document.querySelector('.checkout-btn').addEventListener('click', (event) => {
				event.preventDefault();
				const checkoutLink = document.querySelector('.checkout-link');
				const queryParams = generateCartItemParams();
				checkoutLink.href = `checkout.php?${queryParams}`;
				checkoutLink.click(); // Programmatically trigger the click event
			});
		});
	</script>
	<?php include 'components/alert.php'; ?>
</body>

</html>