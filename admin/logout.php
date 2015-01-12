<?php
include_once('../includes/config.php');
include_once("class.login.php");

$log = new logmein();
$log->encrypt = true; //set encryption
//Log out
$log->logout();

header('Location:'.$config["Url"].'admin/index.php');
?>
