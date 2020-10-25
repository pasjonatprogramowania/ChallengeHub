<?php
session_start();

$_SESSION['logged'] = false;
$_SESSION['username'] = "";
$_SESSION['email'] = "";
$_SESSION['user_id'] = "";
$_SESSION['registered'] = "";

session_destroy();

header("Location: login.php");