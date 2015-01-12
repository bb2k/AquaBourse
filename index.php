<?php
include_once("header.php");
include_once("includes/class.login.php");
include_once("includes/config.php");
include_once("includes/compte.php");


echo '<div class="page">';

if (!file_exists('.ferme')) {

  //instantiate if needed
  $log = new logmein();
  $log->encrypt = true; //set encryption
  if($_REQUEST['action'] == "login"){
    if($log->login("exposant", $_REQUEST['username'], $_REQUEST['password']) == false){
       	displayLoginForm();
    }
  }



  if($log->logincheck($_SESSION['loggedin'], "exposant", "Password", "Mail") == false){
	displayLoginForm();
  } else {
        //do something on successful login
        echo '<div class="loginform">';
	echo '<H2 class="identification">Menu</H2><br/>';
        echo '<li><a href="modification.php">Modifier mon inscription</a><br/></li>';
        echo '<li><a href="annulation.php">Annuler mon inscription</a><br/></li>';
	echo '<li>Changer mon mot de passe<br/></li><br>';
        echo "<lI><a href='".$config["Url"]."/logout.php'>Me deconnecter</a><br/></li>";
        echo '</div>';
  }

} else {
  echo '<div class="loginform">';
  echo "Les inscriptions sont maintenant ferm&eacute;es";
  echo '</div>';
}

echo '</div>';
include_once('footer.php');
?>
