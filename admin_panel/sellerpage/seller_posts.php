<?php
include '../components/connection.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
	header('location: admin_login.php');
}

$get_id = $_GET['post_id'];
//delete post from database
if (isset($_POST['delete'])) {
	$p_id = $_POST['post_id'];
	$p_id = filter_var($p_id, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	$delete_image = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
	$delete_image->execute([$p_id]);
	$fetch_delete_image = $delete_image->fetch(PDO::FETCH_ASSOC);
	if ($fetch_delete_image[''] != '') {
		unlink('../update_image/' . $fetch_delete_image['image']);
	}
	$delete_post = $conn->prepare("DELETE FROM `products` WHERE id=?");
	$delete_post->execute([$p_id]);
	header('location:view_posts.php');
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
			<h1>dashboard</h1>
		</div>
		<div class="title2">
			<a href="index.php">home </a><span>/ dashboard</span>
		</div>
		<section class="read-container">
			<div class="read-post">
				<?php
				$select_posts = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
				$select_posts->execute([$get_id]);
				if ($select_posts->rowCount() > 0) {
					while ($fetch_posts = $select_posts->fetch(PDO::FETCH_ASSOC)) {


				?>
						<form method="post">
							<input type="hidden" name="post_id" value="<?= $fetch_posts['id']; ?>">
							<div class="status" style="background-color: <?php if ($fetch_posts['status'] == 'active') {
																				echo 'limegreen';
																			} else {
																				echo "coral";
																			} ?>;"><?= $fetch_posts['status'] ?></div>
							<?php if ($fetch_posts['image'] != '') { ?>
								<img src="../image/<?= $fetch_posts['image'] ?>" class="image">
							<?php } ?>
							<div class="title"><?= $fetch_posts['name'] ?></div>
							<div class="content"><?= $fetch_posts['product_detail'] ?></div>
							<div style="font-size: 20px; margin-bottom: 10px;">Price: KES <?= $fetch_posts['price'] ?> /-</div>
							<div class="flex-btn">
								<a href="edit_post.php?id=<?= $fetch_posts['id']; ?>" class="btn">edit</a>
								<button type="submit" name="delete" class="btn" onclick="return confirm('delete this post?')">delete</button>
								<a href="view_posts.php" class="btn">go back</a>
							</div>
						</form>
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

	<script type="text/javascript" src="script.js"></script>
</body>

</html>