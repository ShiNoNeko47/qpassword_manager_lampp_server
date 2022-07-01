<?php
session_start();

if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off") {
	$location = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	header('Location: ' . $location);
}

$mysql = mysqli_connect("localhost", "passwords", "pass", "qpassword_manager") or die(mysqli_connect_error());
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	header('Content-Type: application/json; charset=utf-8');
	$action = $_POST['action'];


	if ($action != 'new_user') {
		$user = $_SERVER['PHP_AUTH_USER'];
		$password = $_SERVER['PHP_AUTH_PW'];

		if (!empty($_SESSION)) {
			$user = $_SESSION['username'];
			$password = hash('sha256', $_SESSION['password']);
		}

		$sql = "SELECT ID from Users where (User = \"" . $user . "\" and MasterKey = \"" . $password . "\")";
		$result = mysqli_query($mysql, $sql);

		$UserID = '';
		if ($id = mysqli_fetch_array($result)['ID']) {
			$UserID = $id;
		}
	}

	if ($action == 'get_id') {
		echo $UserID;
	} else if ($action == 'new_user') {
		$user = $_POST['user'];
		$master_key = $_POST['master_key'];
		$sql = 'insert into Users (User, MasterKey) values ("' . $user . '", "' . $master_key . '")';

		try {
			mysqli_query($mysql, $sql);
		} catch (Exception $e) {
			echo mysqli_error($mysql);
		}
	} else if ($action == 'delete') {
		$id = $_POST['id'];
		$sql = 'update Passwords set Deleted = 1 where (ID = ' . $id . ' and UserID = ' . $UserID . ')';
		if (mysqli_query($mysql, $sql)) {
			echo json_encode("Successfully updated");
		} else {
			echo json_encode("Error: " . mysqli_error($mysql));
		}
	} else if ($action == 'add') {
		$password = $_POST['password'];
		$username = $_POST['username'];
		$website = $_POST['website'];
		$sql = 'insert into Passwords (UserID, Website, Username, Password) values (' . $UserID . ', "' . $website . '", "' . $username . '", "' . $password . '")';
		if (mysqli_query($mysql, $sql)) {
			echo "Successfully updated, $sql";
		} else {
			echo "Error: " . mysqli_error($mysql);
		}
	} else if ($action == 'get_pass_ids') {
		$sql = 'select ID from Passwords where (UserID = ' . $UserID . ' and Deleted = 0)';
		$result = mysqli_query($mysql, $sql);
		$data = array();
		while ($row = mysqli_fetch_array($result)) {
			$data[] = $row['ID'];
		}
		echo json_encode($data);
	} else if ($action == 'get_row') {
		$sql = 'SELECT Website, Username, Password from Passwords where (ID = ' . $_POST['id'] . ' and Deleted = 0)';
		$result = mysqli_query($mysql, $sql);
		echo json_encode(mysqli_fetch_array($result));
	} else if ($action == 'create_table') {
		$sql = 'SELECT Website, Username, Password from Passwords where (UserID = ' . $UserID . ' and Deleted = 0)';
		$result = mysqli_query($mysql, $sql);
		$data = array();
		while ($row = mysqli_fetch_array($result)) {
			$data[] = $row;
		}
		echo json_encode($data);
	} else {
		echo 'invalid action';
	}
	mysqli_close($mysql);
	if (!empty($_SESSION)) {
		header("Location:https://" . $_SERVER['HTTP_HOST']);
	}
	exit;
} else {

	$uri = 'https://' . $_SERVER['HTTP_HOST'];

	if (isset($_SESSION['username']) && isset($_SESSION['password'])) {
		$username = $_SESSION['username'];
		$password = hash('sha256', $_SESSION['password']);
		if (isset($_SESSION['confirmpassword'])){
			$sql = 'insert into Users (User, MasterKey) values ("' . $username . '", "' . $password . '")';
			try {
				mysqli_query($mysql, $sql);
			} catch (Exception $e) {
				$_SESSION = array();
				session_destroy();
				header('location:' . $uri . '/signup?signup=failed');
				exit;
			}
			unset($_SESSION['confirmpassword']);
		}
		$sql = "SELECT ID from Users where (User = \"" . $username . "\" and MasterKey = \"" . $password . "\")";
		$result = mysqli_query($mysql, $sql);

		$UserID = '';
		if ($id = mysqli_fetch_array($result)['ID']) {
			$UserID = $id;
		}
		if ($UserID == '') {
			$_SESSION = array();
			session_destroy();
			header('location:' . $uri . '/login?login=failed');
		} else {
			$_SESSION['id'] = $UserID;
			header('Location:' . $uri . '/home');
		}
	} else {
		header('Location:'  . $uri . '/login');
	}

	exit;
}
