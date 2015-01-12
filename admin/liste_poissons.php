<?php

	include_once('../includes/config.php');
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
<center><H1>Liste des poissons</H1></center>
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


	        $sql_recherche_especes = "SELECT Designation,SUM(Quantite) as qte,AVG(Prix) as prxm FROM especes GROUP BY Designation";

		$sth = $dbh->prepare($sql_recherche_especes);
		$sth->execute();
		$result = $sth->fetchAll();
		
		$dbh = null;
          } catch (PDOException $e) {
                print "Error!: " . $e->getMessage() . "<br/>";
                die();
          }

 ?>

	<center><table>
	<tr><td>Especes</td><td>Quantité</td><td>Prix moyen</td></tr>
<?php
	foreach ($result as $row){
		echo "<tr><td>".$row["Designation"]."</td><td>".$row["qte"]."</td><td>".$row["prxm"]."</td></tr>";
	}
?>
	</table></center>

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
