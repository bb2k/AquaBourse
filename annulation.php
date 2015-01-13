<?php
include_once('config/config.php');
include_once('header.php');
include_once("includes/class.login.php");

echo '<div class="page">';
echo '	<div class="loginform">';

echo '<h2 class="identification">Annulation d\'inscrition</h2><br/>';


$log = new logmein();
$log->encrypt = true; //set encryption
//parameters are(SESSION, name of the table, name of the password field, name of the username field)
if($log->logincheck($_SESSION['loggedin'], "exposant", "Password", "Mail") == false){
    //do something if NOT logged in. For example, redirect to login page or display message.

	echo ('<div class="page">');

	displayLoginForm();
	
} else {
	
	if($_REQUEST['action'] == "cancel") {
 		//      $username, $user_table, $pass_column, $user_column
		try {
		        $dbh = new PDO(
                                "mysql:host=".$config["sql_server"].
                                ";port=".$config["sql_port"].
                                ";dbname=".$config["sql_db"], $config['sql_login']
                                , $config['sql_password'], array( PDO::ATTR_PERSISTENT => false));
		        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		        $dbh->exec("SET CHARACTER SET utf8");

			$sql_recherche_exposant = "SELECT Id FROM exposant WHERE Password = '".$_SESSION["loggedin"]."'";
			foreach ($dbh->query($sql_recherche_exposant) as $row) {
                                $id = $row[0];
                        }

		        $sql_delete_exposant = "DELETE FROM exposant WHERE Id='".$id."';DELETE FROM especes  WHERE Id_exposant='".$id."';DELETE FROM bac WHERE Id_exposant='".$id."'"; 
			$dbh->exec($sql_delete_exposant);	

	        	$dbh = null;
			echo "<br>Votre désinscription a été prise en compte";

        	} catch (PDOException $e) {
                	print "Error!: " . $e->getMessage() . "<br/>";
	                die();
        	}
		
	} else {
?>
	        <script language=javascript>
		
		function couleur(obj) {
                            obj.style.backgroundColor = "#FFFFFF";
                        }                       

                function check() {
                        var msg = "";		
			if (document.forms[0].reglement.checked == false) {
                                msg += "Vous devez cocher la case pour confirmer\n";
                                document.forms[0].reglement.style.backgroundColor = "#F3C200";
                        }
			
		

        	        //Si aucun message d'alerte a été initialisé on retourne TRUE
	                if (msg == "") return(true);
 
        		         //Si un message d'alerte a été initialisé on lance l'alerte
	                else    {
                	      alert(msg);
              	 	       return(false);
        	        }
	
		}
		</script>

		<form action="annulation.php" method="post" onSubmit="return check();">
		<input type="hidden" name="action" value="cancel">
		<input type="checkbox" name="reglement" class="reglement" >&nbsp;Oui je veux me désinscrire le la bourse aquariophile</a><br><br>
                <input type="submit" value="Je valide" class="bt_inscription" >
		</form>
<?php
	}
}
	echo "</div>";
	echo "</div>";

include_once('footer.php');
