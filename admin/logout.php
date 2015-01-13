<?php
include_once('../config/config.php');
include_once("class.login.php");

$log = new logmein();
$log->encrypt = true; //set encryption
//Log out
$log->logout();

header('Location:'.$config["url"].'admin/index.php');
?>
