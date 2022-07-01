<!DOCTYPE html>
<html>

<head>
	<link rel="stylesheet" type="text/css" href="login.css">
</head>

<body>
<?php
if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off") {
	$location = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	header('Location: ' . $location);
}
$fail = "";
if (isset($_GET["signup"])) {
	$fail = "Username is already taken";
}
session_start();
if (isset($_POST['username']) && isset($_POST['password'])) {
	$_SESSION['username'] = $_POST['username'];
	$_SESSION['password'] = $_POST['password'];
	$_SESSION['confirmpassword'] = $_POST['confirmpassword'];
	if ($_SESSION['password'] == $_SESSION['confirmpassword']) {
		header("location:" . 'https://' . $_SERVER['HTTP_HOST']);
	}
	else {
		$fail = "Passwords don't match";
	}
}
if (isset($_SESSION['username']) && isset($_SESSION['password'])) {
	header("location:" . 'https://' . $_SERVER['HTTP_HOST']);
}
?>
	<form method="post" action="index.php" class="outer">
		<?php
		echo "<p class=\"warning\">" . $fail . "</p>";
		?>
		<div>
			Username:
			<input type="text" name="username" /><br>
			<br>
		</div>
		<div>
			Password:
			<input type="password" name="password" /><br>
			<br>
		</div>
		<div>
			Confirm Password:
			<input type="password" name="confirmpassword" /><br>
			<br>
		</div>
		<input type="submit" value="Signup" />or<a href=<?php echo '"https://' . $_SERVER['HTTP_HOST'] . '/login"' ?>>Login</a><br>
	</form>
</body>

</html>
