<?php
include "header.php";
include "includes/formInscr.php";
?>

<?php
if (isset($_POST["action"])) {
	echo '<div class="page">';
	echo '<div class="affiche">';
	echo '<img src=images/affiche.jpg width=400>';
	echo '</div>';

	echo '<div class="loginform">';
	echo '<div class="logo"><img src="images/logo.png" width=140></div>';
        echo '<h2 class="identification">Inscription</h2><br/><br>';
	$retour = traite_formulaire();
	if ($retour > 0){
		echo "Votre inscription a été prise en compte.<br>";
		echo "Vous allez recevoir un mail permettant de confirmer votre inscription.<br>";
	} else {
		echo 'Erreur dans l\'inscription<br>';
	}
	echo '<br><a href="index.php">Retour à la page d\'accueil</a>';
	echo '</div>';
	echo '</div></div>';
} else {
	affiche_form();
}
?>

<?php
include "footer.php";
?>

