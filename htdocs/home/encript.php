<?php

require 'vendor/autoload.php';
use Fernet\Fernet;

session_start();

$key = shell_exec("./generate_fernet_key.py " . $_SESSION["password"]);
$fernet = new Fernet($key);
echo $fernet->encode($_GET['password']);
