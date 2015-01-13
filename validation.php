<?php
include_once('header.php');
include_once('config/config.php');

echo '<div class="page">';
echo '<div class="affiche">';
echo '  <img src=images/affiche.jpg width=400>';
echo '</div>';

echo '<div class="loginform">';
echo '<div class="logo"><img src="images/logo.png" width=140></div>';

echo ' <h2 class="identification">Validation du compte</h2><br/>';

if (isset($_GET["hash"])){
		$id_exposant = -1;
		$dbh = new PDO(
                                "mysql:host=".$config["sql_server"].
                                ";port=".$config["sql_port"].
                                ";dbname=".$config["sql_db"], $config['sql_login']
                                , $config['sql_password'], array( PDO::ATTR_PERSISTENT => false));

                $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $dbh->exec("SET CHARACTER SET utf8");


                $getidbyhash = "SELECT `ID` FROM `exposant` WHERE `Password` like '".$_GET["hash"]."' AND Confirmation = '0'";

                foreach ($dbh->query($getidbyhash) as $row) {
                        $id_exposant = $row['ID'];
                }
		if ($id_exposant > 0){
			$query = "update `exposant` SET `Confirmation`='1' where `Password` like '".$_GET["hash"]."'";
			$dbh->exec($query);
			echo "<br>Votre compte est validé.";
		        echo "<br/><br/><a href='index.php'>Retour à la page principale</a><br/><br/>";

		} else {
			echo "<br>Ce code de validation est inexistant ou le compte est déjà validé.";
		        echo "<br/><br/><a href='index.php'>Retour à la page principale</a><br/><br/>";
		}
} else {
	echo "<br>Vous devez sp&eacute;cifier un code à valider.";
        echo "<br/><br/><a href='index.php'>Retour à la page principale</a><br/><br/>";
}

echo '</div></div>';

include_once('footer.php');
?>
