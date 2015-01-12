<?php
include_once("header.php");
include_once("class.login.php");
include_once("../includes/config.php");


function displayLogin(){
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
}





echo '<div class="page">';
//instantiate if needed
$log = new logmein();
$log->encrypt = true; //set encryption
if($_REQUEST['action'] == "login"){
    if($log->login("users", $_REQUEST['username'], $_REQUEST['password']) == false){
       	displayLogin();
    }
}



if($log->logincheck($_SESSION['loggedinadmin'], "users", "Password", "Mail") == false){
	displayLogin();
} else {
        //do something on successful login
        echo '<div class="loginform">';
	echo '<H2 class="identification">Administration</H2><br/>';
        echo '<li><a href="liste_exposants.php">Liste des exposants</a><br/></li>';
        echo '<li><a href="liste_poissons.php">Liste des poissons</a><br/></li>';
        echo "<lI><a href='".$config["Url"]."admin/logout.php'>Me deconnecter</a><br/></li>";
        echo '</div>';
}



echo '</div>';
include_once('footer.php');

?>
