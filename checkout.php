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

// Check if any product data is present in the query params
if (!empty($_GET)) {
	$sql = "";
	$stmt = null;

	foreach ($_GET as $param => $value) {
		if (strpos($param, 'product_') === 0) {
			$parts = explode('_', $param);
			$productId = $parts[1];
			$key = $parts[2];

			// Using the $productId to identify the product and while $key to identify the type of data (price or qty)
			// Build the SQL query based on the key
			if ($key === 'price') {
				$sql = "UPDATE cart SET price = :newValue WHERE product_id = :productId";
			} elseif ($key === 'qty') {
				$sql = "UPDATE cart SET qty = :newValue WHERE product_id = :productId";
			}

			// Prepare the statement only if SQL query is built
			if (!empty($sql)) {
				$stmt = $conn->prepare($sql);
				$newValue = ($key === 'price') ? (float) $value : (int) $value;
				$stmt->bindParam(':newValue', $newValue);
				$stmt->bindParam(':productId', $productId);
				$stmt->execute();

				// Reset the SQL query for the next iteration
				$sql = "";
			}
		}
	}
}

try {
	if (isset($_POST['place-order-btn'])) {
		if (empty($user_id)) {
			$warning_msg[] = 'please login first';
		} else {
			$name = $_POST['name'];
			$name = filter_var($name, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
			$number  = $_POST['phone'];
			$number = filter_var($number, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
			$email = $_POST['email'];
			$email = filter_var($email, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
			$address = $_POST['flat'] . ', ' . $_POST['street'] . ', ' . $_POST['city'] . ', ' . $_POST['county'] . ', ' . $_POST['pincode'];
			$address = filter_var($address, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
			$address_type = $_POST['address_type'];
			$address_type = filter_var($address_type, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
			$method = $_POST['method'];
			$method = filter_var($method, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
			$varify_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id=?");
			$varify_cart->execute([$user_id]);

			if (isset($_GET['get_id'])) {
				$get_product = $conn->prepare("SELECT * FROM `products` WHERE id=? LIMIT 1");
				$get_product->execute([$_GET['get_id']]);
				if ($get_product->rowCount() > 0) {
					while ($fetch_p = $get_product->fetch(PDO::FETCH_ASSOC)) {
						$orderNo = unique_orderId();
						$_SESSION['orderNo'] = $orderNo;
						$_SESSION['phone'] = $number;

						// Reduce product quantity here
						$new_quantity = $fetch_p['qty'] - 1;
						if ($new_quantity >= 0) {
							$update_quantity = $conn->prepare("UPDATE `products` SET qty=? WHERE id=?");
							$update_quantity->execute([$new_quantity, $_GET['get_id']]);
						} else {
							$warning_msg[] = 'Product out of stock';
							// Handle the out-of-stock scenario appropriately
						}

						$insert_order = $conn->prepare("INSERT INTO `orders`(id, user_id, name, number, email, address, address_type, method, product_id, price, qty, order_no) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)");
						$insert_order->execute([unique_id(), $user_id, $name, $number, $email, $address, $address_type, $method, $fetch_p['id'], $fetch_p['price'], 1, $orderNo]);
						if($method == "cash on delivery"){
						  	header('location: order.php');
						} else {
						header('location: final_checkout.php');
						}
					}
				} else {
					$warning_msg[] = 'somthing went wrong';
				}
			} elseif ($varify_cart->rowCount() > 0) {
				while ($f_cart = $varify_cart->fetch(PDO::FETCH_ASSOC)) {
					$orderNo = unique_orderId();
					$_SESSION['orderNo'] = $orderNo;
					$_SESSION['phone'] =  $number;
					// Reduce product quantity here
					$get_product = $conn->prepare("SELECT * FROM `products` WHERE id=? LIMIT 1");
					$get_product->execute([$f_cart['product_id']]);
					if ($get_product->rowCount() > 0) {
						$fetch_p = $get_product->fetch(PDO::FETCH_ASSOC);

					$new_quantity = $fetch_p['qty'] - $f_cart['qty'];
					if ($new_quantity >= 0) {
						$update_quantity = $conn->prepare("UPDATE `products` SET qty=? WHERE id=?");
						$update_quantity->execute([$new_quantity, $f_cart['product_id']]);
					} else {
						$warning_msg[] = 'Product out of stock';
						// Handle the out-of-stock scenario appropriately
					}

				}


					$insert_order = $conn->prepare("INSERT INTO `orders`(id, user_id, name, number, email, address, address_type, method, product_id, price, qty, order_no) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)");
					$insert_order->execute([unique_id(), $user_id, $name, $number, $email, $address, $address_type, $method, $f_cart['product_id'], $f_cart['price'], $f_cart['qty'], $orderNo]);
					if($method == "cash on delivery"){
						header('location: order.php');
				  } else {
				 		header('location: final_checkout.php');
				  }
				}
				if ($insert_order) {
					$delete_cart_id = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
					$delete_cart_id->execute([$user_id]);
					if($method == "cash on delivery"){
						header('location: order.php');
				  } else {
				  		header('location: final_checkout.php');
				  }
				}
			} else {
				$warning_msg[] = 'somthing went wrong/ you need data to checkout';
			}
		}
	}
} catch (PDOException $e) {
	$warning_msg[] = $e->getMessage();
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
	<title>Store: Checkout</title>
</head>

<body>
	<?php include 'components/header.php'; ?>
	<div class="main">
		<div class="banner">
			<h1>checkout summary</h1>
		</div>
		<div class="title2">
			<a href="index.php">home </a><span>/ checkout summary</span>
		</div>
		<section class="checkout">
			<div class="title">
				<h1>checkout summary</h1>
				<p>please fill the form to place your order
				</p>
			</div>
			<div class="row">
				<form method="post">
					<h3>billing details</h3>
					<div class="flex">
						<div class="box">
							<div class="input-field">
								<p>your name <span>*</span></p>
								<input type="text" name="name" placeholder="Enter Your name" class="input">
								<small></small>
							</div>

							<div class="input-field">
								<p>your number <span>*</span></p>
								<input name="phone" placeholder="07XX XXX XXX">
								<small></small>
							</div>

							<div class="input-field">
								<p>your email <span>*</span></p>
								<input type="email" name="email" placeholder="Enter Your email" class="input">
								<small></small>
							</div>
							<div class="input-field">
								<p>payment method <span>*</span></p>
								<select name="method" class="input">
									<option value="cash on delivery">cash on delivery</option>
									<option value="Lipa na Mpesa">Lipa na Mpesa</option>
								</select>
							</div>
							<div class="input-field">
								<p>address type <span>*</span></p>
								<select name="address_type" class="input">
									<option value="home">home</option>
									<option value="office">office</option>

								</select>
							</div>
						</div>
						<div class="box">
							<div class="input-field">
								<p>address line 01 <span>*</span></p>
								<input type="text" name="flat" placeholder="e.g flat & building number" class="input">
								<small></small>
							</div>
							<div class="input-field">
								<p>address line 02 <span>*</span></p>
								<input type="text" name="street" placeholder="e.g street name" class="input">
								<small></small>
							</div>
							<div class="input-field">
								<p>Town name<span>*</span></p>
								<input type="text" name="city" placeholder="Enter your city name" class="input">
								<small></small>
							</div>
							<div class="input-field">
								<p>county name <span>*</span></p>
								<input type="text" name="county" placeholder="Enter your city name" class="input">
								<small></small>
							</div>
							<div class="input-field">
								<p>pincode <span>*</span></p>
								<input type="text" name="pincode" required maxlength="6" placeholder="110022" min="0" max="999999" class="input">
								<small></small>
							</div>
						</div>
					</div>
					<button type="submit" name="place-order-btn" class="btn">place order</button>
				</form>
				<div class="summary">
					<a href="cart.php" class="btn" style="margin-top: 24px;width:19%; text-align: center; height: 3rem; margin-top: .7rem;">go back</a>

					<h3>my bag</h3>
					<div class="box-container">
						<?php
						$grand_total = 0;
						if (isset($_GET['get_id'])) {
							$select_get = $conn->prepare("SELECT * FROM `products` WHERE id=?");
							$select_get->execute([$_GET['get_id']]);
							while ($fetch_get = $select_get->fetch(PDO::FETCH_ASSOC)) {
								$sub_total = $fetch_get['price'];
								$grand_total += $sub_total;
								$_SESSION['amount'] =  $grand_total;
						?>
								<div class="flex">
									<img src="admin_panel/image/<?= $fetch_get['image']; ?>" class="image">
									<div>
										<h3 class="name"><?= $fetch_get['name']; ?></h3>
										<p class="price">Ksh .<?= number_format($fetch_get['price'], 2); ?></p>
									</div>
								</div>
								<?php
							}
						} else {
							$select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id=?");
							$select_cart->execute([$user_id]);
							if ($select_cart->rowCount() > 0) {
								while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) {
									$select_products = $conn->prepare("SELECT * FROM `products` WHERE id=?");
									$select_products->execute([$fetch_cart['product_id']]);
									$fetch_product = $select_products->fetch(PDO::FETCH_ASSOC);
									$sub_total = ($fetch_cart['qty'] * $fetch_product['price']);
									$grand_total += $sub_total;
									$_SESSION['amount'] =  $grand_total;

								?>
									<div class="flex">
										<img src="admin_panel/image/<?= $fetch_product['image']; ?>">
										<div>
											<h3 class="name"><?= $fetch_product['name']; ?></h3>
											<p class="price"><?= $fetch_product['price']; ?> X <?= $fetch_cart['qty']; ?></p>
										</div>
									</div>
						<?php
								}
							} else {
								echo '<p class="empty">your cart is empty</p>';
							}
						}
						?>
					</div>
					<div class="grand-total"><span>total amount payable: </span>Ksh. <?= number_format($grand_total, 2); ?>/-</div>
				</div>
			</div>
		</section>
		<?php include 'components/footer.php'; ?>
	</div>
	<script src="script.js"></script>
	<script>
		const nameEl = document.querySelector('[name="name"]'),
			emailEl = document.querySelector('[name="email"]'),
			phoneEl = document.querySelector('[name="phone"]'),
			addrln1El = document.querySelector('[name="flat"]'),
			addrln2El = document.querySelector('[name="street"]'),
			ctyEl = document.querySelector('[name="city"]'),
			countyEl = document.querySelector('[name="county"]'),
			pincdeEl = document.querySelector('[name="pincode"]'),
			btn = document.querySelector('[name="place-order-btn"]');

		const isRequired = value => Boolean(value);

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

		const checkName = () => isValid(nameEl, /^[a-zA-Z\s]+$/, 'Enter full name');

		const checkEmail = () => isValid(emailEl, /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/, 'Email is not valid.');

		const checkPhoneInput = () => isValid(phoneEl, /^(0[1-9][0-9]{8})$/, "Invalid Phone Number");

		const checkAddr1 = () => isValid(addrln1El, /^[a-zA-Z\s\d\(\),.-]+$/, 'Enter valid flat & building number');

		const checkAddr2 = () => isValid(addrln2El, /^[a-zA-Z\s\d\(\),.-]+$/, 'Enter valid street name');

		const checkCity = () => isValid(ctyEl, /^[a-zA-Z\s]+$/, 'City should only contain letters and be between 4 and 15 characters in length. Digits are not allowed');

		const checkCountyName = () => isValid(countyEl, /^[a-zA-Z\s]+$/, 'County field should only contain letters and be between 4 and 15 characters in length. Digits are not allowed');

		const checkPinCode = () => isValid(pincdeEl, /^\d{4,10}$/, 'Pincode should only contain digits of between 4 and 22 characters or more in length');

		btn.addEventListener('click', (e) => {
			let isValidForm = true;

			if (!checkName()) {
				isValidForm = false;
			}

			if (!checkEmail()) {
				isValidForm = false;
			}

			if (!checkPhoneInput()) {
				isValidForm = false;
			}

			if (!checkAddr1()) {
				isValidForm = false;
			}

			if (!checkAddr2()) {
				isValidForm = false;
			}
			if (!checkCity()) {
				isValidForm = false;
			}

			if (!checkCountyName()) {
				isValidForm = false;
			}

			if (!checkPinCode()) {
				isValidForm = false;
			}

			if (!isValidForm) {
				e.preventDefault();
			}
		});
	</script>
	<?php include 'components/alert.php'; ?>
</body>

</html>