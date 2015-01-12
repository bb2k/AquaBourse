<?php
require_once('includes/config.php');

function displayLoginForm(){
	?>
	        <div class="loginform">
        <?php
	echo '<h2 class="identification">Identification</h2>';
        $log = new logmein();
        $log->encrypt = true; //set encryption
        //parameters here are (form name, form id and form action)
        $log->loginform("login", "loginformid", "index.php");
?>
        <br/><br/>
        Pas inscrit ? <strong><a href="inscription.php">formulaire d'inscription</a></strong>
        <br>J'ai oublié mon <a href="reset.php">mot de passe </a>
        </div>
<?php
}


function wd_generatePassword($length=8, $possible='$=@#23456789bcdfghjkmnpqrstvwxyz')
{
    $password = '';
    $possible_length = strlen($possible) - 1;

    #
    # add random characters to $password for $length
    #

    while ($length--)
    {
        #
        # pick a random character from the possible ones
        #

        $except = substr($password, -$possible_length / 2);

        for ($n = 0 ; $n < 5 ; $n++)
        {
            $char = $possible{mt_rand(0, $possible_length)};

            #
            # we don't want this character if it's already in the password
            # unless it's far enough (half of our possible length).
            # note: we have 4 tries to find a suitable one.
            #

            if (strpos($except, $char) === false)
            {
                break;
            }
        }

        $password .= $char;
    }

    return $password;
}

function envoiMailModification($recipient, $pass){
        global $config;
        

        // Envoi d'un mail de confirmation
        $Name = $config["mail_name"] ; //senders name
        $email = $config["mail_from"]; //senders e-mail adress
        $mail_body = "
        <html><body>
        Bonjour,<br/>
        Vous avez demandé à modifier votre inscription à la bourse annuelle du Club Rennais Aquariophile.<br/>
        Merci de confirmer cette modification en cliquant sur le lien ci dessous ou en le copiant dans la barre d'adresse de votre navigateur.<br/>
                <a href=".$config["Url"]."validation.php?hash=$pass>".$config["Url"]."/validation.php?hash=$pass</a></br>
        Votre identifiant est : $recipient<br/>
        Cordialement,<br/><br/>
        Le Webmaster<br/>
        </body></html>
        "; //mail body
        $subject = "Validation Modification ".$config["Evenement"]; //subject
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= "From: ". $Name . " <" . $email . ">\r\n"; //optional headerfields

        mail($recipient, $subject, $mail_body, $headers); //mail command :) 
}


function envoiMailValidation($recipient, $pass){
	global $config;
	$hash = md5($pass);

        // Envoi d'un mail de confirmation
        $Name = $config["mail_name"] ; //senders name
        $email = $config["mail_from"]; //senders e-mail adress
        $mail_body = "
	<html><body>
	Bonjour,<br/>
	Vous avez demandé à vous inscrite à la bourse annuelle du Club Rennais Aquariophile.<br/>
	Merci de confirmer cette inscription en cliquant sur le lien ci dessous ou en le copiant dans la barre d'adresse de votre navigateur.<br/>
		<a href=".$config["Url"]."/validation.php?hash=$hash>".$config["Url"]."validation.php?hash=$hash</a></br>
	Votre identifiant est : $recipient<br/>
	Votre mot de passe : $pass<br/>
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

?>
