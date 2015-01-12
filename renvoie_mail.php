<?php
include "header.php";
require_once('includes/config.php');
include "includes/compte.php";

function renvoiMailValidation($recipient,$hash){
	global $config;

        // Envoi d'un mail de confirmation
        $Name = $config["mail_name"] ; //senders name
        $email = $config["mail_from"]; //senders e-mail adress
        $mail_body = "
	<html><body>
	Bonjour,<br/>
	
	Nous petite applis d'inscrition ayant eu des soucis lors de l'envoi du mail de validation, voici un second mail avec une URL correcte permettant de
	Valider votre inscription.<br/>

	Vous avez demandé à vous inscrite à la bourse annuelle du Club Rennais Aquariophile.<br/>
	Merci de confirmer cette inscription en cliquant sur le lien ci dessous ou en le copiant dans la barre d'adresse de votre navigateur.<br/>
		<a href=".$config["Url"]."/validation.php?hash=$hash>".$config["Url"]."/validation.php?hash=$hash</a></br>
	Votre identifiant est : $recipient<br/>
	Cordialement,<br/><br/>
	Le Webmaster<br/>
	</body></html>
	"; //mail body
        $subject = "Validation ".$config["Evenement"]; //subject
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= "From: ". $Name . " <" . $email . ">\r\n"; //optional headerfields

        mail($recipient, $subject, $mail_body, $headers); //mail command :) 
}


try {

        $dbh = new PDO(
                                "mysql:host=".$config["sql_server"].
                                ";port=".$config["sql_port"].
                                ";dbname=".$config["sql_db"], $config['sql_login']
                                , $config['sql_password'], array( PDO::ATTR_PERSISTENT => false));
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbh->exec("SET CHARACTER SET utf8");


        $sql_recherche_exposant = "SELECT Mail,Password FROM exposant WHERE Confirmation=0";

	$sth = $dbh->prepare($sql_recherche_exposant);
	$sth->execute();
	$result = $sth->fetchAll();

	
	$dbh = null;

	foreach ($result as $row){	
		echo "Envoi d'un mail à ".$row["Mail"]."\n";
		renvoiMailValidation($row["Mail"],$row["Password"]);
	}


} catch (PDOException $e) {
        print "Error!: " . $e->getMessage() . "<br/>";
       die();
}

include "footer.php";
?>
