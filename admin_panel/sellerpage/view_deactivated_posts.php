<?php
include '../components/connection.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
	header('location: admin_login.php');
}

//delete post from database

if (isset($_POST['delete'])) {
	$p_id = $_POST['product_id'];
	$p_id = filter_var($p_id, FILTER_SANITIZE_FULL_SPECIAL_CHARS);


	$delete_post = $conn->prepare("DELETE FROM `products` WHERE id = ?");
	$delete_post->execute([$p_id]);

	$message[] = 'post deleted successfully';
}

//search post
if (isset($_POST['search_btn'])) {
	$search = $_POST['search'];
	$search = filter_var($search, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

	$search_post = $conn->prepare("SELECT * FROM `products` WHERE status =? AND name LIKE ?");
	$search_post->execute(['deactive', '%' . $search . '%']);

	if ($search_post->rowCount() > 0) {
		$message[] = 'search result';
	} else {
		$warning_msg[] = 'no result found';
	}
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
			<h1>deactivated products</h1>
		</div>
		<div class="title2">
			<a href="dashboard.php">home </a><span>/ all products</span>
		</div>
		<section class="post-editor">
			<form method="post" class="flex-btn">
				<input type="text" name="search" placeholder="search by name" class="search">
				<input type="submit" name="search_btn" value="search" class="btn">
			</form>
			<?php
			if (isset($message)) {
				foreach ($message as $message) {
					echo '
							<div class="message">
								<span>' . $message . '</span>
								<i class="bx bx-x" onclick="this.parentElement.remove();"></i>
							</div>
						';
				}
			}
			?>
			<div class="show-post">
				<div class="box-container">
					<?php
					if (isset($search)) {
						if ($search_post->rowCount() > 0) {
							while ($fetch_post = $search_post->fetch(PDO::FETCH_ASSOC)) {
					?>
								<form method="post" class="box">
									<input type="hidden" name="product_id" value="<?= $fetch_posts['id']; ?>">
									<?php if ($fetch_post['image'] != '') { ?>
										<img src="../image/<?= $fetch_post['image'] ?>" class="image">
									<?php } ?>
									<div class="status" style="color: <?php if ($fetch_posts['status'] == 'active') {
																			echo 'limegreen';
																		} else {
																			echo "coral";
																		} ?>;">
										<?= $fetch_post['status'] ?>
									</div>
									<div class="price">KES <?= $fetch_post['price'] ?>/-</div>
									<div class="title"><?= $fetch_post['name'] ?></div>
									<div class="flex-btn">
										<a href="edit_post.php?id=<?= $fetch_posts['id']; ?>" class="btn">edit</a>
										<button type="submit" name="delete" class="btn" onclick="return confirm('delete this post?')">delete</button>
										<a href="read_posts.php?post_id=<?= $fetch_posts['id']; ?>" class="btn">view post</a>
									</div>
								</form>
					<?php
							}
						} else {
							echo '
							<div class="empty">
								<p>no result found! <br><a href="add_posts.php" class="btn" style="margin-top: 10px;">add post</a></p>
							</div>
						';
						}
					}
					?>
				</div>
			</div>

			<h1 class="heading">your deactivated posts</h1>
			<div class="show-post">
				<div class="box-container">
					<?php
					$select_posts = $conn->prepare("SELECT * FROM `products` WHERE status = ?");

					$select_posts->execute(["deactive"]);
					if ($select_posts->rowCount() > 0) {
						while ($fetch_posts = $select_posts->fetch(PDO::FETCH_ASSOC)) {


					?>
							<form method="post" class="box">
								<input type="hidden" name="product_id" value="<?= $fetch_posts['id']; ?>">
								<?php if ($fetch_posts['image'] != '') { ?>
									<img src="../image/<?= $fetch_posts['image'] ?>" class="image">
								<?php } ?>
								<div class="status" style="color: <?php if ($fetch_posts['status'] == 'active') {
																		echo 'limegreen';
																	} else {
																		echo "coral";
																	} ?>;"><?= $fetch_posts['status'] ?></div>
								<div class="price">KES <?= $fetch_posts['price'] ?>/-</div>
								<div class="title"><?= $fetch_posts['name'] ?></div>
								<div class="flex-btn">
									<a href="edit_post.php?id=<?= $fetch_posts['id']; ?>" class="btn">edit</a>
									<button type="submit" name="delete" class="btn" onclick="return confirm('delete this post?')">delete</button>
									<a href="read_posts.php?post_id=<?= $fetch_posts['id']; ?>" class="btn">view post</a>
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
		</section>
	</div>

	<script type="text/javascript" src="script.js"></script>
</body>

</html>