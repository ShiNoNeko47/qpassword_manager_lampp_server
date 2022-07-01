<?php
session_start();
$_SESSION = array();
session_destroy();
header('Location:https://' . $_SERVER['HTTP_HOST']);
