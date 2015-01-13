<?php
include_once("header.php");
include_once("includes/class.login.php");
include_once("config/config.php");
include_once("includes/compte.php");


echo '<div class="page">';


echo '<div class="affiche">';
echo '<img src=images/affiche.jpg width=400>';
echo '</div>';

echo '<div class="loginform">';
echo '<div class="logo"><img src="images/logo.png" width=140></div>';


if (!file_exists('.ferme')) {

  $errMsg="";

  //instantiate if needed
  $log = new logmein();
  $log->encrypt = true; //set encryption

  if($_REQUEST['action'] == "login"){
    if($log->login("exposant", $_REQUEST['username'], $_REQUEST['password']) == false){
       $errMsg="Erreur d'identification";
    }        
  } 

  if($log->logincheck($_SESSION['loggedin'], "exposant", "Password", "Mail") == true){
        echo '<H2 class="identification">Menu</H2><br/>';
        echo '<li><a href="modification.php">Modifier mon inscription</a><br/></li>';
        echo '<li><a href="annulation.php">Annuler mon inscription</a><br/></li>';
        echo '<li>Changer mon mot de passe<br/></li><br>';
        echo "<li><a href='".$config["url"]."/logout.php'>Me deconnecter</a><br/></li>";
  } else {
    displayLoginForm($errMsg);
  }



} else {
  echo '<H2 class="identification">Identification</H2><br/>';	
  echo "Les inscriptions sont maintenant ferm&eacute;es";
}

echo '</div></div>';
include_once('footer.php');
?>
