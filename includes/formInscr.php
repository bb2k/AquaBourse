<?php


/*
 *
 * Libraries des fonctions du formulaire d'inscription
 *
 * Club Rennais Aquriophile - Décembre 2011
 *
 */

require_once('includes/config.php');
require_once('includes/compte.php');

/*
 *	Gestion de l'inscription après validation du formulaire
 *
 */
function traite_formulaire() {
	global $config;


	// Controle des valeurs
	try {
        $dbh = new PDO(
                                "mysql:host=".$config["sql_server"].
                                ";port=".$config["sql_port"].
                                ";dbname=".$config["sql_db"], $config['sql_login']
                                , $config['sql_password'], array( PDO::ATTR_PERSISTENT => false));
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbh->exec("SET CHARACTER SET utf8");


        $sql_recherche_exposant = "SELECT count(Mail) FROM exposant WHERE Mail = '".$_POST["email"]."'";

        foreach ($dbh->query($sql_recherche_exposant) as $row) {
                        $count = $row[0];
        }

        if ($count > 0 ) {
                echo("Un exposant est déjà inscrit sous cet Email<br>");
		echo('<a href="" onClick="history.back();"><-- Retour</a> - ');
		echo('<a href="index.php" >Page Principale</a><br/>');        
		exit(-1);
	}

        $dbh = null;
        } catch (PDOException $e) {
                print "Error!: " . $e->getMessage() . "<br/>";
                die();
        }
	

	//  Composition des données
	$exposant['Nom'] 	 = mysql_escape_string($_POST["nom"]);
	$exposant["Prenom"] 	 = mysql_escape_string($_POST["prenom"]);
	$exposant["Adresse"] 	 = mysql_escape_string($_POST["adresse"]);
	$exposant["Adresse2"]    = mysql_escape_string($_POST["adresse2"]);
	$exposant["CodePostal"]  = mysql_escape_string($_POST["codepostal"]);
	$exposant["Ville"] 	 = mysql_escape_string($_POST["ville"]);
	$exposant["Telephone"] 	 = mysql_escape_string($_POST["telfixe"]);
	$exposant["Portable"] 	 = mysql_escape_string($_POST["telportable"]);
	$exposant["Mail"] 	 = mysql_escape_string($_POST["email"]);
	$exposant["Date"] 	 = date("Y-m-d");
        $exposant["CleanPass"] 	 = wd_generatePassword();
        $exposant["Password"] 	 = md5($exposant["CleanPass"]);
        $exposant["Commentaire"] = mysql_escape_string($_POST["commentaires"]);      

        // Les poissons
	$nb=1;
        
	$i_pois = 1;
	while (isset($_POST["des$nb"])) {
		if ($_POST["des$nb"] != '') {

    		    $exposant["poisson"][$i_pois]["Designation"]	= mysql_escape_string($_POST["des$nb"]);
		    $exposant["poisson"][$i_pois]["Taille"] 		= $_POST["taille$nb"];
		    $exposant["poisson"][$i_pois]["Prix"] 		= $_POST["prix$nb"];
		    $exposant["poisson"][$i_pois]["Quantite"] 		= $_POST["quantite$nb"];
		    $exposant["poisson"][$i_pois]["Eau"] 		= $_POST["type$nb"];
			$i_pois++;
		}
		$nb++;
	}

	// Les bacs
	$nbbacs=1;
	for ($i=1; $i < 4; $i++) {
	    if ($_POST["nature$i"] != '') {
	 	
		$exposant["bac"][$nbbacs]["Nature"] 		= mysql_escape_string($_POST["nature$i"]);
		$exposant["bac"][$nbbacs]["Temperature"] 	= $_POST["temp$i"];
		$exposant["bac"][$nbbacs]["Details"] 		= mysql_escape_string($_POST["renseignement$i"]); 
		$nbbacs++;
	    }
	}

	$dbh = new PDO(
                                "mysql:host=".$config["sql_server"].
                                ";port=".$config["sql_port"].
                                ";dbname=".$config["sql_db"], $config['sql_login']
                                , $config['sql_password'], array( PDO::ATTR_PERSISTENT => false));
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbh->exec("SET CHARACTER SET utf8");

        // Génération des requetes SQL
	$sql_insert_exposant = "INSERT INTO `exposant` (`Nom` ,`Prenom` ,`Adresse`, `Adresse2` ,`CodePostal` ,`Ville` ,`Telephone` ,`Portable` ,`Mail` ,`Password` ,`Confirmation` ,`Commentaire` ,`Date`) 
                                VALUES ('".
					$exposant["Nom"]."','".
					$exposant["Prenom"]."','".
					$exposant['Adresse']."','".
					$exposant['Adresse2']."','".
					$exposant['CodePostal']."','".
					$exposant['Ville']."','".
					$exposant['Telephone']."','".
					$exposant['Portable']."','".
					$exposant['Mail']."','".
					$exposant['Password']."',0,'".
					$exposant["Commentaire"]."','".
					$exposant['Date'].
                                "')";

	// Inscription dans la base
	try {
		$count = $dbh->exec($sql_insert_exposant);


		$id__exposant =  0;
		$getidbymail = "SELECT `ID` FROM `exposant` WHERE `Mail` like '".$exposant['Mail']."'";

		foreach ($dbh->query($getidbymail) as $row) {
			$id_exposant = $row['ID'];
		}
		

		// On insere les poissons de l'exposant dans la base
		$sql_insert_poissons = "INSERT INTO `especes` (`Designation` ,`Taille` ,`Prix` ,`Quantite` ,`Eau`,`Id_exposant`)
	                                VALUES ";
	        	$max=count($exposant["poisson"]);
		

	        for ( $i=1 ; $i<=$max ; $i++ ) {
        	        $sql_insert_poissons .= "('".
						$exposant["poisson"][$i]["Designation"]."','".
                	                        $exposant["poisson"][$i]["Taille"]."','".
                        	                $exposant["poisson"][$i]["Prix"]."','".
                                	        $exposant["poisson"][$i]["Quantite"]."','".
                                        	$exposant["poisson"][$i]["Eau"]."','".
                                        	$id_exposant."')";
			if($i == $max){
				$sql_insert_poissons.=";";
			} else {
				$sql_insert_poissons.=",";
			}
	        }
		$count = $dbh->exec($sql_insert_poissons);		


		// On insere les bacs de l'expossant
		$sql_insert_bac = "INSERT INTO `bac` (`Nature` ,`Temperature` ,`Details` ,`Id_exposant`)
                                        VALUES ";
		$max=count($exposant["bac"]);
		if ($max > 0) {
    		    for ( $i=1 ; $i<=$max ; $i++ ) {
	                $sql_insert_bac .= "('".
        	                                $exposant["bac"][$i]["Nature"]."','".
                                                $exposant["bac"][$i]["Temperature"]."','".
                                                $exposant["bac"][$i]["Details"]."','".
                                                $id_exposant."')";
			
               	        if($i == $max){
                       	        $sql_insert_bac.=";";
                        } else {
       	                        $sql_insert_bac.=",";
               	        }
		    }
                
                    $count = $dbh->exec($sql_insert_bac);	
		}

		$dbh = null;
	} catch (PDOException $e) {
                print "Error!: " . $e->getMessage() . "<br/>";
                die();
        }

	envoiMailValidation($exposant['Mail'],$exposant["CleanPass"]);
	return TRUE;
}





/*
 *   Gestion des données du formulaire en vue de la modification d'une inscription
 *
 */

function traite_modif_formulaire() {
	global $config;


	//  Composition des données
	$exposant['Nom'] 	 = mysql_escape_string($_POST["nom"]);
	$exposant["Prenom"] 	 = mysql_escape_string($_POST["prenom"]);
	$exposant["Adresse"] 	 = mysql_escape_string($_POST["adresse"]);
	$exposant["Adresse2"]    = mysql_escape_string($_POST["adresse2"]);
	$exposant["CodePostal"]  = mysql_escape_string($_POST["codepostal"]);
	$exposant["Ville"] 	 = mysql_escape_string($_POST["ville"]);
	$exposant["Telephone"] 	 = mysql_escape_string($_POST["telfixe"]);
	$exposant["Portable"] 	 = mysql_escape_string($_POST["telportable"]);
	$exposant["Mail"] 	 = mysql_escape_string($_POST["email"]);
        $exposant["Commentaire"] = mysql_escape_string($_POST["commentaires"]);      

        // Les poissons
	$nb=1;
        
	$i_pois = 1;
	while (isset($_POST["des$nb"])) {
		if ($_POST["des$nb"] != '') {

    		    $exposant["poisson"][$i_pois]["Designation"]	= mysql_escape_string($_POST["des$nb"]);
		    $exposant["poisson"][$i_pois]["Taille"] 		= $_POST["taille$nb"];
		    $exposant["poisson"][$i_pois]["Prix"] 		= $_POST["prix$nb"];
		    $exposant["poisson"][$i_pois]["Quantite"] 		= $_POST["quantite$nb"];
		    $exposant["poisson"][$i_pois]["Eau"] 		= $_POST["type$nb"];
			$i_pois++;
		}
		$nb++;
	}

	// Les bacs
	$nbbacs=1;
	for ($i=1; $i < 4; $i++) {
	    if ($_POST["nature$i"] != '') {
	 	
		$exposant["bac"][$nbbacs]["Nature"] 		= mysql_escape_string($_POST["nature$i"]);
		$exposant["bac"][$nbbacs]["Temperature"] 	= $_POST["temp$i"];
		$exposant["bac"][$nbbacs]["Details"] 		= mysql_escape_string($_POST["renseignement$i"]); 
		$nbbacs++;
	    }
	}

	$dbh = new PDO(
                                "mysql:host=".$config["sql_server"].
                                ";port=".$config["sql_port"].
                                ";dbname=".$config["sql_db"], $config['sql_login']
                                , $config['sql_password'], array( PDO::ATTR_PERSISTENT => false));
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbh->exec("SET CHARACTER SET utf8");

        // Génération des requetes SQL
	$sql_update_exposant = "UPDATE `exposant`
				SET `Nom` = '".$exposant['Nom']."',
				`Prenom`     = '".$exposant['Prenom']."',
				`Adresse`	 = '".$exposant['Adresse']."',
				`Adresse2`	 = '".$exposant['Adresse2']."',
				`CodePostal` = '".$exposant['CodePostal']."',
				`Ville`	 = '".$exposant['Ville']."',
				`Telephone`	 = '".$exposant['Telephone']."',
				`Portable`	 = '".$exposant['Portable']."',
				`Mail`	 = '".$exposant['Mail']."',
				`Confirmation` = 0,
				`Commentaire`= '".$exposant['Commentaire']."'
				WHERE `Password` = '".$_SESSION['loggedin']."'";

        
	// Inscription dans la base
	try {
		$count = $dbh->exec($sql_update_exposant);

		$id__exposant =  0;
		$getidbymail = "SELECT `ID` FROM `exposant` WHERE `Mail` like '".$exposant['Mail']."'";

		foreach ($dbh->query($getidbymail) as $row) {
			$id_exposant = $row['ID'];
		}
		
		$sql_delete_poissons = "DELETE FROM `especes` WHERE id_exposant=$id_exposant";
        	$count = $dbh->exec($sql_delete_poissons);

		// On insere les poissons de l'exposant dans la base
		$sql_insert_poissons = "INSERT INTO `especes` (`Designation` ,`Taille` ,`Prix` ,`Quantite` ,`Eau`,`Id_exposant`)
	                                VALUES ";

		$max=count($exposant["poisson"]);
	        for ( $i=1 ; $i<=$max ; $i++ ) {
        	        $sql_insert_poissons .= "('".
						$exposant["poisson"][$i]["Designation"]."','".
                	                        $exposant["poisson"][$i]["Taille"]."','".
                        	                $exposant["poisson"][$i]["Prix"]."','".
                                	        $exposant["poisson"][$i]["Quantite"]."','".
                                        	$exposant["poisson"][$i]["Eau"]."','".
                                        	$id_exposant."')";
			if($i == $max){
				$sql_insert_poissons.=";";
			} else {
				$sql_insert_poissons.=",";
			}
	        }

	
		$count = $dbh->exec($sql_insert_poissons);		


		// On insere les bacs de l'expossant

		$sql_delete_bacs = "DELETE FROM `bac` WHERE id_exposant=$id_exposant";
        	$count = $dbh->exec($sql_delete_bacs);


		$sql_insert_bac = "INSERT INTO `bac` (`Nature` ,`Temperature` ,`Details` ,`Id_exposant`)
                                        VALUES ";
		$max=count($exposant["bac"]);
		if ($max > 0) {
    		    for ( $i=1 ; $i<=$max ; $i++ ) {
	                $sql_insert_bac .= "('".
        	                                $exposant["bac"][$i]["Nature"]."','".
                                                $exposant["bac"][$i]["Temperature"]."','".
                                                $exposant["bac"][$i]["Details"]."','".
                                                $id_exposant."')";
			
               	        if($i == $max){
                       	        $sql_insert_bac.=";";
                        } else {
       	                        $sql_insert_bac.=",";
               	        }
		    }
                
                    $count = $dbh->exec($sql_insert_bac);	
		}

		$dbh = null;
	} catch (PDOException $e) {
                print "Error!: " . $e->getMessage() . "<br/>";
                die();
        }

	envoiMailModification($exposant['Mail'],$_SESSION['loggedin']);
	return TRUE;
}




/*
 *  Affiche le formulaire d'inscription/modification
 *	Paramètres : 
 *		$values  -> tableau de valeurs correspondant au entrées du formulaire
 *		$action  -> chaine de caractère : "inscription" ou "modification"
 *		        	Si modification alors $values ne peut être vide
 */


function affiche_form($values = array(), $action='inscription') {
?>
	<script type="text/javascript" src="jquery.fancybox-1.3.4/jquery-1.4.3.min.js"></script>
	<script type="text/javascript" src="jquery.fancybox-1.3.4/fancybox/jquery.easing-1.3.pack.js"></script>
	<script type="text/javascript" src="jquery.fancybox-1.3.4/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>        	
	<script type="text/javascript" src="jquery.fancybox-1.3.4/fancybox/jquery.fancybox-1.3.4.js"></script>
	<link rel="stylesheet" type="text/css" href="jquery.fancybox-1.3.4/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
	
	<script language=javascript>
                compteur = 2;
			

                function AddOneRow(){
                    var newRow = document.getElementById('matable').insertRow(-1);
                    var newCell = newRow.insertCell(0);
                                newCell.innerHTML = '<td><input size="50" name="des'+compteur+'"></td>';
                                newCell = newRow.insertCell(1);
                                newCell.innerHTML = '<td align="center"><input name="type'+compteur+'" value="D" type="radio" class="radio"></td>';
                                newCell = newRow.insertCell(2);
                                newCell.innerHTML = '<td align="center"><input name="type'+compteur+'" value="M" type="radio" class="radio"></td>';
                                newCell = newRow.insertCell(3);
                                newCell.innerHTML = '<td><input size="5" name="taille'+compteur+'" class="taille"></td>';
                                newCell = newRow.insertCell(4);
                                newCell.innerHTML = '<td><input size="5" name="prix'+compteur+'" class="prix"></td>';
                                newCell = newRow.insertCell(5);
                                newCell.innerHTML = '<td><input size="5" name="quantite'+compteur+'" class="qte"></td>';
				//newCell = newRow.insertCell(6);
				//newCell.innerHTML = '<td><input value="-"  type="button" onclick="RemoveRow('+compteur+')"></td>';
                                compteur++;
                 } 

			function RemoveRow(i){
				var myTD=document.getElementById('matable').getElementsByTagName('tr')[i];
				myTD.parentNode.removeChild(myTD);
			}

 
			function couleur(obj) {
 			    obj.style.backgroundColor = "#FFFFFF";
			}			

			function check() {
				var msg = "";
 
				//Vérification du mail s'il n'est pas vide on vérifie le . et @
	 
				if (document.forms[0].email.value != "")	{
					indexAroba = document.forms[0].email.value.indexOf('@');
					indexPoint = document.forms[0].email.value.indexOf('.');
					if ((indexAroba < 0) || (indexPoint < 0))		{
 
						//dans le cas ou il manque soit le . soit l'@ on modifie la couleur d'arrière plan du champ mail et définissons un message d'alerte
 
						document.forms[0].email.style.backgroundColor = "#F3C200";
						msg += "Le mail est incorrect\n";
					}
				}
 
				//Notre champs mail est vide donc on change la couleur et on défini un autre message d'alerte
 
				else	{
					document.forms[0].email.style.backgroundColor = "#F3C200";
					msg += "Veuillez saisir votre mail.\n";
				}
 
				//ici nous vérifions si le champs nom et vide, changeons la couleur du champs et définissons un message d'alerte
				if (document.forms[0].nom.value == "")	{
					msg += "Veuillez saisir votre nom\n";
					document.Form.nom.style.backgroundColor = "#F3C200";
				}
 
				//meme manipulation pour le champ prénom
				if (document.forms[0].prenom.value == "")	{
					msg += "Veuillez saisir votre prenom\n";
					document.forms[0].prenom.style.backgroundColor = "#F3C200";
				}
 
				// On verifie que le reglement est accepté
				if (document.forms[0].reglement.checked == false) {
					msg += "Vous devez lire et accepter le réglement\n";
                                        document.forms[0].reglement.style.backgroundColor = "#F3C200";

				}

				// On verifie l'existence d'au moins 1 poisson
				if (document.forms[0].des1.value == '') {
                                        msg += "Vous devez inscrire au moins un type de poisson !\n";
                                        document.forms[0].des1.style.backgroundColor = "#F3C200";

                                }

				//Si aucun message d'alerte a été initialisé on retourne TRUE
				if (msg == "") return(true);
 
				//Si un message d'alerte a été initialisé on lance l'alerte
				else	{
					alert(msg);
					return(false);
				}
			}		

			jQuery(document).ready(function() {
			      $(".fancy").fancybox({
				  'width'			: '85%',
				  'height'			: '85%',      
  				  'transitionIn'		: 'elastic',
				  'transitionOut'		: 'elastic',
		  		  'type'			: 'iframe'
				});            
	
			});

        </script>

<div class="page_inscription">
<div class="inscription_header"></div>
<div class="inscription">
<div class="content_inscription">
	<form action="<?php echo("$action")?>.php" name="Form" method="post" onSubmit="return check();">
	        <H2>Etat civil :</H2>
                <hr>
			<div class="civil_gauche">
                          <input type="hidden" name="action" value="<?php echo($action);?>">
                          <span class="txt_inscription_1">Nom</span><input name="nom" value="<?php echo $values["nom"]?>" onKeyUp="javascript:couleur(this);"><br>
                          <span class="txt_inscription_1">Pr&eacute;nom </span><input name="prenom" value="<?php echo $values["prenom"]?>" onKeyUp="javascript:couleur(this);"><br>
			  <span class="txt_inscription_1">T&eacute;l&eacute;phone Fixe</span><input name="telfixe" value="<?php echo $values["telfixe"]?>"><br>
                          <span class="txt_inscription_1">T&eacute;l&eacute; Portable </span><input name="telportable" value="<?php echo $values["telportable"]?>"><br>
                          <span class="txt_inscription_1">Courriel</span><input size="50" name="email" onKeyUp="javascript:couleur(this);" value="<?php echo $values["email"]?>"><br><br>
			</div>
			<div class="civil_droite">
                          <span class="txt_inscription_1">Adresse </span><input size="30" name="adresse" value="<?php echo $values["adresse"]?>"><br>
                          <span class="txt_inscription_1">Adresse2</span><input size="30" name="adresse2" value="<?php echo $values["adresse2"]?>"><br>
                          <span class="txt_inscription_1">Code Postal</span><input size="5" name="codepostal" value="<?php echo $values["codepostal"]?>"><BR>
			  <span class="txt_inscription_1">Ville </span><input size="30" name="ville" value="<?php echo $values["ville"]?>"><br>
			</div>
                        
                        <H2 class="clear">Poissons :</H2>
                        <hr>

                        <table id='matable'>
                        <tbody>
                                <tr>
                                        <td>D&eacute;signation</td>
                                        <td>Eau Douce</td>
                                        <td>Eau de mer</td>
                                        <td>Taille</td>
                                        <td>Prix</td>
                                        <td>Quantit&eacute;</td>
                                </tr>
				<tr>
                                        <td><input size="50" name="des1" value="<?php echo $values["des1"]?>"></td>
					<?php
					if ($values["type1"] == 'M' ) {
                                                echo('<td  align="center"><input name="type1" value="D" class="radio" type="radio"></td>');
                                                echo('<td align="center"><input name="type1" value="M" checked="checked" type="radio" class="radio"></td>');
					} else {
                                                echo('<td align="center"><input name="type1" value="D" checked="checked" type="radio" class="radio"></td>');
                                                echo('<td align="center"><input name="type1" value="M" class="radio" type="radio"></td>');
		
					}

					?>
                                        <td><input size="5" name="taille1" value="<?php echo $values["taille1"]?>" class="taille"></td>
                                        <td><input size="5" name="prix1" value="<?php echo $values["prix1"]?>" class="prix"></td>
                                        <td><input size="5" name="quantite1" value="<?php echo $values["quantite1"]?>" class="qte"></td>
                                </tr>
				<?php
				$i=2;
				while(isset($values["des".$i])){
					echo('<td><input size="50" name="des'.$i.'" value="'.$values["des".$i].'"></td>');
					if ($values["type".$i] == 'M' ) {
	                                     	echo('<td align="center"><input name="type'.$i.'" value="D" type="radio" class="radio"></td>');
        	                                echo('<td align="center"><input name="type'.$i.'" value="M" checked="checked" type="radio" class="radio"></td>');
					} else {
						echo('<td align="center"><input name="type'.$i.'" value="D" checked="checked" type="radio" class="radio"></td>');
                                                echo('<td align="center"><input name="type'.$i.'" value="M" type="radio" class="radio"></td>');
					}
                                        echo('<td><input size="5" name="taille'.$i.'" value="'.$values["taille".$i].'" class="taille"></td>');
                                        echo('<td><input size="5" name="prix'.$i.'" value="'.$values["prix".$i].'" class="prix"></td>');
                                        echo('<td><input size="5" name="quantite'.$i.'"value="'.$values["quantite".$i].'" class="qte"></td>');
					$i++;
				}
				?>
                        </tbody>
                        </table>
                        <input value="Ajouter"  type="button" onclick='AddOneRow();' class="ajouter">
			<br/><br/>
                        <H2>R&eacute;servation de bacs (Max 3) avec emplacements inclus :</H2>
			<hr>
                        <table>
                        <tbody>
                                <tr>
                                        <td>Nature</td>
                                        <td>Temp&eacute;rature</td>
                                        <td>Autres renseignements utiles</td>
                                </tr>
                                <tr>
                                        <td><input size="50" name="nature1" value="<?php echo $values["nature1"]?>" ></td>
                                        <td><input size="5"  name="temp1" value="<?php echo $values["temp1"]?>"></td>
                                        <td><input size="50" name="renseignement1" value="<?php echo $values["renseignement1"]?>" class="renseignement"></td>
                                </tr>
                                <tr>
                                        <td><input size="50" name="nature2" value="<?php echo $values["nature2"]?>"></td>
                                        <td><input size="5"  name="temp2" value="<?php echo $values["temp2"]?>"></td>
                                        <td><input size="50" name="renseignement2" value="<?php echo $values["renseignement2"]?>" class="renseignement"></td>
                                </tr>
                                <tr>
                                        <td><input size="50" name="nature3" value="<?php echo $values["nature3"]?>"></td>
                                        <td><input size="5"  name="temp3" value="<?php echo $values["temp3"]?>"></td>
                                        <td><input size="50" name="renseignement3" value="<?php echo $values["renseignement3"]?>" class="renseignement"></td>
                           </tr>

                        </tbody>
                        </table><br>
                        <H2>Commentaires :</H2>
                        <hr>
                        <textarea cols="100" rows="3" name="commentaires"><?php echo $values["commentaires"]?></textarea>
			<br/><br/>
                        
			<input type="checkbox" name="reglement" class="reglement" >&nbsp;J'ai lu et j'accepte le <a class="fancy" href='reglement.php' >reglement</a>
                        <input type="submit" value="Je valide" class="bt_inscription" >
            </form>
		<br><a href="index.php">Retour à la page d'accueil</a>
</DIV>
</div>
	<div class="inscription_footer">
	</div>   
</div>


<?php
}
?>
