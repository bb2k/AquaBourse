<?php
include "config/config.php";
include "header.php";
include "includes/formInscr.php";
include("includes/class.login.php");

$log = new logmein();
$log->encrypt = true; //set encryption
//parameters are(SESSION, name of the table, name of the password field, name of the username field)
if($log->logincheck($_SESSION['loggedin'], "exposant", "Password", "Mail") == false){
    //do something if NOT logged in. For example, redirect to login page or display message.
	header('Location:'.$config["url"].'index.php');
} else {

	if (isset($_POST["action"])) {
	        echo ('<div class="page">');
		echo ('<div class="page_inscription">');
		echo '<div class="loginform"> <h2 class="identification">Modification</h2><br/><br>';
		$retour = traite_modif_formulaire();
		if ($retour > 0){
			echo "Vos modifications ont été prises en compte.<br>";
			echo "Vous allez recevoir un mail permettant de confirmer ces modifications<br>";
		} else {
			echo 'Erreur dans l\'inscription<br>';
		}
	        echo '<br><a href="index.php">Retour à la page d\'accueil</a>';
	        echo '</div></div></div>';
	
	} else {
  	  try {
 	       $dbh = new PDO(
                                "mysql:host=".$config["sql_server"].
                                ";port=".$config["sql_port"].
                                ";dbname=".$config["sql_db"], $config['sql_login']
                                , $config['sql_password'], array( PDO::ATTR_PERSISTENT => false));
        	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        	$dbh->exec("SET CHARACTER SET utf8");


	        $sql_recherche_exposant = "SELECT * FROM exposant WHERE Password = '".$_SESSION["loggedin"]."'";

		$sth = $dbh->prepare($sql_recherche_exposant);
		$sth->execute();
		$result = $sth->fetchAll();
		
		$tableau["nom"] = $result[0]["Nom"];
		$tableau["prenom"] = $result[0]["Prenom"];
		$tableau["adresse"] = $result[0]["Adresse"];
		$tableau["adresse2"] = $result[0]["Adresse2"];
		$tableau["codepostal"] = $result[0]["CodePostal"];
		$tableau["ville"] = $result[0]["Ville"];
		$tableau["telfixe"] = $result[0]["Telephone"];
		$tableau["telportable"] = $result[0]["Portable"];
		$tableau["email"] = $result[0]["Mail"];
		$tableau["commentaires"] = $result[0]["Commentaire"];

		$sql_recherche_bac = 'SELECT * FROM bac WHERE id_exposant='.$result[0]["ID"];
		$sth = $dbh->prepare($sql_recherche_bac);
                $sth->execute();
                $resultbac = $sth->fetchAll();
	
		$tableau["nature1"] = $resultbac[0]["Nature"];
		$tableau["temp1"] = $resultbac[0]["Temperature"];
		$tableau["renseignement1"] = $resultbac[0]["Details"];

		$tableau["nature2"] = $resultbac[1]["Nature"];
                $tableau["temp2"] = $resultbac[1]["Temperature"];
                $tableau["renseignement2"] = $resultbac[1]["Details"];

		$tableau["nature3"] = $resultbac[2]["Nature"];
                $tableau["temp3"] = $resultbac[2]["Temperature"];
                $tableau["renseignement3"] = $resultbac[2]["Details"];

		$sql_recherche_especes = 'SELECT * FROM especes WHERE id_exposant='.$result[0]["ID"];
                $sth = $dbh->prepare($sql_recherche_especes);
                $sth->execute();
                $resultespeces = $sth->fetchAll();

		$i=1;
		foreach ($resultespeces as $row){
			$tableau["des".$i]=$row["Designation"];
			$tableau["type".$i]=$row["Eau"];
			$tableau["taille".$i]=$row["Taille"];
			$tableau["prix".$i]=$row["Prix"];
                        $tableau["quantite".$i]=$row["Quantite"];

			$i++;
		}

		$dbh = null;
          } catch (PDOException $e) {
                print "Error!: " . $e->getMessage() . "<br/>";
                die();
          }

	  affiche_form($tableau, "modification");
	}
	

	include "footer.php";
}
?>

