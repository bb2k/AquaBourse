<?php

	include_once('../config/config.php');
	include_once("class.login.php");
	include_once('header.php');



$log = new logmein();
$log->encrypt = true; //set encryption
//parameters are(SESSION, name of the table, name of the password field, name of the username field)
if($log->logincheck($_SESSION['loggedinadmin'], "users", "Password", "Mail") == false){
    //do something if NOT logged in. For example, redirect to login page or display message.

        echo ('<div class="page">');
?>
	<div class="loginform">
<?php     
	echo '<h2 class="identification">Identification Administration</h2>';
        $log = new logmein();
        $log->encrypt = true; //set encryption
        //parameters here are (form name, form id and form action)
        $log->loginform("login", "loginformid", "index.php");
?>
        <br/><br/>
        </div>

<?php
        echo '</div>    ';
} else {

?>
<center><H1>Liste des exposants</H1></center>
<br><br>


<div class="page_inscription">
<div class="inscription_header"></div>
<div class="inscription">
<div class="content_inscription">

<?php
	  try {
 	       $dbh = new PDO(
                                "mysql:host=".$config["sql_server"].
                                ";port=".$config["sql_port"].
                                ";dbname=".$config["sql_db"], $config['sql_login']
                                , $config['sql_password'], array( PDO::ATTR_PERSISTENT => false));
        	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        	$dbh->exec("SET CHARACTER SET utf8");


	        $sql_recherche_exposant = "SELECT * FROM exposant ORDER BY Nom";

		$sth = $dbh->prepare($sql_recherche_exposant);
		$sth->execute();
		$result = $sth->fetchAll();
		
		$dbh = null;
          } catch (PDOException $e) {
                print "Error!: " . $e->getMessage() . "<br/>";
                die();
          }

 ?>

	<table>
	<tr><td>Nom</td><td>Prénom</td><td>Mail</td><td>Adresse</td><td>Confirmé</td><td>Poissons</td><td>Bacs</td><td>Commentaire</td></tr>
<?php
	foreach ($result as $row){
		if ($row["Confirmation"] == 1 ) {
			$conf="Oui";
		} else {
			$conf="Non";
		}

		if ($row["Adresse2"] == "") {
			$add2 = "";
		} else {
			$add2 = $row["Adresse2"]."<br>";
		}

		echo "<tr><td>".$row["Nom"]."</td><td>".$row["Prenom"]."</td><td>".$row["Mail"]."</td><td>".$row["Adresse"]."<br>".$add2.$row["CodePostal"]." ".$row["Ville"]."</td><td>".$conf."</td>";
		$commentaire=$row["Commentaire"];
		try {
	               $dbh = new PDO(
                                "mysql:host=".$config["sql_server"].
                                ";port=".$config["sql_port"].
                                ";dbname=".$config["sql_db"], $config['sql_login']
                                , $config['sql_password'], array( PDO::ATTR_PERSISTENT => false));
        	        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                	$dbh->exec("SET CHARACTER SET utf8");


	                $sql_recherche_especes = "SELECT * FROM especes WHERE Id_exposant=".$row["ID"];

        	        $sth = $dbh->prepare($sql_recherche_especes);
                	$sth->execute();
        	        $poissons = $sth->fetchAll();
	          
			echo "<td>";
			foreach ($poissons as $p){
				echo $p["Designation"].",".$p["Quantite"].",".$p["Eau"]."<br>";
			}      
			echo "</td>";


			$sql_recherche_bacs = "SELECT * FROM bac WHERE Id_exposant=".$row["ID"];

                        $sth = $dbh->prepare($sql_recherche_bacs);
                        $sth->execute();
                        $bac = $sth->fetchAll();

                        echo "<td>";
                        foreach ($bac as $b){
                                echo $b["Nature"].",".$b["Temperature"]."°C"."<br>";
                        }
                        echo "</td>";



                	$dbh = null;
	          } catch (PDOException $e) {
        	        print "Error!: " . $e->getMessage() . "<br/>";
                	die();
	          }
		  echo "<td>$commentaire</td>";
	   	  echo "</tr>";
	}
?>
	</table>

	<br><a href="index.php">Retour</a><br>

</DIV>
</div>
	<div class="inscription_footer">
	</div>   
</div>

<br>
<?php
}
	include_once('footer.php');
?>
