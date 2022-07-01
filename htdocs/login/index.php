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
if (isset($_GET["login"])) {
	$fail = "Wrong username or password";
}
session_start();
if (isset($_POST['username']) && isset($_POST['password'])) {
	$_SESSION['username'] = $_POST['username'];
	$_SESSION['password'] = $_POST['password'];
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
		<input type="submit" value="Login" />or<a href=<?php echo '"https://' . $_SERVER['HTTP_HOST'] . '/signup"' ?>>Signup</a><br>
	</form>
</body>

</html>
