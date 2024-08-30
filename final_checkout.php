<?php
include 'components/connection.php';


include 'payments/express-stk.php';

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

$orderNo = isset($_SESSION['orderNo']) ? $_SESSION['orderNo'] : '';
$number = isset($_SESSION['phone']) ? $_SESSION['phone'] : '';
$amount = isset($_SESSION['amount']) ? $_SESSION['amount'] : '';

$formarttedNumber = $strippedNumber = ltrim($number, '0');

$_SESSION['phoneNo'] = $formarttedNumber;



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
            <h1>Final step: Payment checkout</h1>
        </div>
        <div class="title2">
            <a href="index.php">home </a><span>/ Payment checkout</span>
        </div>
        <section class="checkout">
            <div class="title">
                <h1>Payment checkout</h1>
            </div>
            <div class="row">
                <div class="summary">
                    <a href="checkout.php" class="btn" style="margin-top: 24px;width:19%; text-align: center; height: 3rem; margin-top: .7rem;">go back</a>

                    <div class="box-container">
                        <?php
                        $grand_total = 0;
                        if (isset($_GET['get_id'])) {
                            $select_get = $conn->prepare("SELECT * FROM `products` WHERE id=?");
                            $select_get->execute([$_GET['get_id']]);
                            while ($fetch_get = $select_get->fetch(PDO::FETCH_ASSOC)) {
                                $sub_total = $fetch_get['price'];
                                $grand_total += $sub_total;

                        ?>
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

                                ?>

                        <?php
                                }
                            } else {
                                echo '<p class="empty">your cart is empty</p>';
                            }
                        }
                        ?>
                    </div>
                    <div class="grand-total"><span>total amount payable: </span>Ksh. 1 /-</div>
                    <form method="post" class="fn-chk">
                        <h3>confirm details</h3>
                        <p>1. Confirm/Enter the <b>phone number</b> and press "<b>Confirm and Pay</b>"</br>2. You will receive a popup on your phone. Enter your <b>MPESA PIN</b></p>

                        <div class="flex">
                            <div class="box">
                                <div class="input-field">
                                    <input type="hidden" name="amount" value="<?= $amount ?>" />

                                    <input type="hidden" name="orderNo" value="<?= $orderNo ?>" />
                                    <p>Phone number<span>*</span></p>
                                    <div class="ui labeled input" id="phonenumber">
                                        <div class="ui basic label" id="phonenumber254">254</div>
                                        <input id="phonenumber_input" type="number" name="MSISDN" placeholder="7XX XXX XXX" value="<?= $formarttedNumber ?>" onkeypress="if(this.value.length==9) return false;">
                                        <br>
                                        <small></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" name="pay-btn" class="btn">Confirm and Pay</button>
                    </form>
                </div>


            </div>
        </section>
        <?php include 'components/footer.php'; ?>
    </div>
    <script src="script.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const phoneEl = document.querySelector('[name="MSISDN"]');
            const btn = document.querySelector('[name="pay-btn"]');

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
                    showError(input, 'Phone number cannot be blank.');
                    return false;
                } else if (!pattern.test(value)) {
                    showError(input, message);
                    return false;
                } else {
                    showSuccess(input);
                    return true;
                }
            };

            const checkPhoneInput = () => isValid(phoneEl, /^\d{9}$/, "Invalid Phone Number");

            phoneEl.addEventListener('input', checkPhoneInput);

            btn.addEventListener('click', (e) => {
                if (!checkPhoneInput()) {
                    e.preventDefault();
                }
            });
        });
    </script>

    <?php include 'components/alert.php'; ?>
</body>

</html>