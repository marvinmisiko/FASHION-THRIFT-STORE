
<header class="header">
	<div class="flex">
		<a href="dashboard.php" class="logo">
			<strong style="font-size: 40px; text-decoration: none; color: black;">STORE</strong>
		</a>
		<nav class="navbar">
			<a href="dashboard.php">dashboard</a>
			<a href="add_category.php">add category</a>
			<a href="add_posts.php">add product</a>
			<a href="view_posts.php">view post</a>
			<a href="user_accounts.php">accounts</a>
			<a href="reports.php">reports</a>
		</nav>
		<div class="icons">
			<img src="../icons/user.png" id="user-btn" style="height: 35px; margin-left: 5px;"/>
			<img src="../icons/list.png" id="menu-btn" style="height: 25px"/>
		</div>
		<div class="profile-detail">
			<?php 
				$select_profile = $conn->prepare("SELECT * FROM `admin` WHERE id=?");
				$select_profile->execute([$admin_id]);
				if ($select_profile->rowCount() > 0) {
					$fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
			?>
			<div class="profile">
				<img src="../image/<?= htmlspecialchars($fetch_profile['profile']); ?>" class="logo-image" width="100">
				<p><?= htmlspecialchars($fetch_profile['name']); ?></p>
			</div>
			<div class="flex-btn">
				<a href="update_profile.php" class="btn">update profile</a>
				<a href="../components/admin_logout.php" onclick="return confirm('logout from this website')" class="btn">logout</a>
			</div>
			<?php } ?>
		</div>
	</div>
</header>





