<?php
include_once('config/config.php');
include_once('header.php');
include_once("includes/class.login.php");

echo '<div class="page">';

echo '<div class="affiche">';
echo '<img src=images/affiche.jpg width=400>';
echo '</div>';

echo '<div class="loginform">';
echo '<div class="logo"><img src="images/logo.png" width=140></div>';

	echo '<br/><h2 class="identification">Mot de passe oublié</h2><br/>';
	
        $log = new logmein();
        $log->encrypt = true; //set encryption


if($_REQUEST['action'] == "resetlogin") {
 	//      $username, $user_table, $pass_column, $user_column
	echo "Un mail contenant votre nouveau mot de passe<br/>vient de vous être envoyé à l'adresse :<br/>";
	echo "<strong><center>".$_REQUEST['username']."</center></strong><br/>";
	$log->passwordreset($_REQUEST['username'],'exposant', 'Password','Mail');
	echo "<br/><br/><a href='index.php'>Retour à la page principale</a>";
} else {
	
	$log->resetform("resetformname", "resetformid", "reset.php");
}

	echo "</div>";
	echo "</div>";

include_once('footer.php');
