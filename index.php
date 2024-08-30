<?php 
 include 'components/connection.php';
 session_start();
 if (isset($_SESSION['user_id'])) {
		$user_id = $_SESSION['user_id'];
	}else{
		$user_id = '';
	}

	if (isset($_POST['logout'])) {
		session_destroy();
		header("location: login.php");
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
	
	<title>Store - Home Page</title>
</head>
<body>
	<?php include 'components/header.php'; ?>
	<div class="main">
		
		<section class="home-section">
		<div class="hero_model">
			<div style="text-align: center; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
				<h1 style="color: white; font-size : 40px; padding: 10px;">WELCOME TO THE CUEA FASHION THRIFT STORE</h1>
				<p style="color: white; font-size: 20px; padding: 5px">Once in a lifetime offers!</p>
				<a href="view_products.php" class="btn">Thrift now</a>
				
			</div>
		</section>
		<!-- home slider end -->
		<section class="thumb">
			
		</section>
	
		<?php include 'components/footer.php'; ?>
	</div>
	<script src="script.js"></script>
	<?php include 'components/alert.php'; ?>
</body>
</html>