<!DOCTYPE html>
<html>

<head>
	<link rel="stylesheet" type="text/css" href="style.css" />
	<script type="text/javascript">
		var passwords = []

		function func() {
			let pass = document.getElementById("pass").value
			let pass2 = document.getElementById("pass2").value
			if (pass == pass2) {
				xmlhttp = new XMLHttpRequest()
				xmlhttp.onreadystatechange = function() {
					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
						document.getElementById("password").value = xmlhttp.responseText
					}
				}
				xmlhttp.open("GET", "encript.php?password=" + pass, true)
				xmlhttp.send()
			}
		}

		function toggle(id) {
			password = document.getElementById("p" + id).innerHTML
			if (password == "*".repeat(password.length)) {
				document.getElementById("p" + id).innerHTML = passwords["p" + id]
			} else {
				document.getElementById("p" + id).innerHTML = "*".repeat(password.length)
				passwords["p" + id] = password
			}
		}

		function hideall() {
			i = document.getElementById("i").innerHTML
			for (j = 0; j < i; j++) {
				id = document.getElementById(j).innerHTML
				toggle(id)
			}
		}
	</script>
</head>

<body onload="hideall()">
	<?php
	require 'vendor/autoload.php';

	use Fernet\Fernet;

	session_start();
	//$_SESSION = array();
	//session_destroy();
	if (empty($_SESSION)) {
		header('Location:https://' . $_SERVER['HTTP_HOST']);
	}
	if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off") {
		$location = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		header('Location: ' . $location);
	}
	$key = shell_exec("./generate_fernet_key.py " . $_SESSION["password"]);
	$fernet = new Fernet($key); ?>
	<p>
	<form style="display: inline-block;" method="post" action="logout.php">
		<label style="color: #fff;">You're signed in as<?php echo " " . $_SESSION['username'] ?></label>
		<input type="submit" value="Log out">
	</form>
	</p>
	<form class="add" method="post" action=<?php echo "\"https://" . $_SERVER['HTTP_HOST'] . "\"" ?>>
		<input type="hidden" value="add" name="action" />
		<div>
			<label for="website">Website:</label>
			<input type="text" name="website" /><br>
		</div>
		<div>
			<label for="username">Username:</label>
			<input type="text" name="username" /><br>
		</div>
		<div>
			<label>Password:</label>
			<input type="password" id="pass" oninput="func()" /><br>
		</div>
		<div>
			<label>Confirm Password:</label>
			<input type="password" id="pass2" oninput="func()" /><br>
		</div>
		<input type="hidden" value="" name="password" id="password" />
		<div style="display: flex; justify-content: right; width: 100%;">
			<input style="width:100%" type="submit" value="Add" id="button" />
		</div><br>
	</form>

	<?php

	$mysql = mysqli_connect("localhost", "passwords", "pass", "qpassword_manager") or die(mysqli_connect_error());
	$sql = 'SELECT ID, Website, Username, Password from Passwords where (UserID = ' . $_SESSION['id'] . ' and Deleted = 0)';
	$result = mysqli_query($mysql, $sql);
	$data = array();
	$i = 0;
	while ($row = mysqli_fetch_array($result)) {
		if ($i == 0) {
			echo "<table class=\"pass\">";
			echo "<tr>";
			echo "<td>Website</td>";
			echo "<td>Username</td>";
			echo "<td>Password</td>";
		}
		$data[] = $row;
		echo "<tr>";
		echo "<td>" . $row['Website'] . "</td>";
		echo "<td>" . $row['Username'] . "</td>";
		echo "<td id=\"p" . $row["ID"] . "\" onclick=\"toggle(" . $row["ID"] . ")\" >" . $fernet->decode($row['Password']) . "</td>";
		echo "<td>"; ?>

		<form method="post" action=<?php echo "\"https://" . $_SERVER['HTTP_HOST'] . "\"" ?>>
			<input type="hidden" value="delete" name="action" />
			<input type="hidden" value=<?php echo "\"" . $row['ID'] . "\"" ?> name="id" />
			<input type="submit" value="Delete" />
		</form>

	<?php
		echo "</td>";
		echo "<td id=\"$i\" style=\"visibility:hidden;\">" . $row["ID"] . "</td>";
		$i++;
		echo "</tr>";
	}
	if($i == 0){
		echo "<h3 style=\"color: #fff;\">You don't have any passwords saved yet</h3>";
	}
	echo "</table>";
	echo "<p id=\"i\" style=\"visibility:hidden;\">" . $i . "</td>"; ?>

</body>

</html>
