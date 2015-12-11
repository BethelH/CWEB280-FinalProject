<?php

function my_autoloader($className) {
    require_once( "classes/$className.class.php" );
}

spl_autoload_register("my_autoloader");
session_start();


//server side validation of the email
function checkEmailRegex() {
    return (preg_match('/^\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,3}$/', $_POST["email"]));
}

// Loads the form for the user to fill in their email
function loadForm($formPass) {

    $form = new HtmlForm();
    $form->renderStart("resetFRM", "Enter your Email Address To Recover Password");
    echo "<div>";
    $form->renderTextbox("email", "Email", true, 50, "", "pattern='^\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,3}$'");
    echo"<span id = 'sMail'></span>";
    echo "</div >";
    $form->renderSubmitEnd("resetForm", "Reset Password");
}

//Sends the message to a hard-coded email address and stores the random string

function sendMail($username) {
    $db = new DbObject();
//  <a href='doReset.php?resetid= . $randString' >Click Here to Reset Password</a>
    //generates random string and stores it in a variable
    $randString = getToken();
    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $headers .= 'From: Classified Staff <classifiedWebmasters@classified.com>' . "\r\n";
    // get the folder name where the reset file is contained
    $folder= dirname($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
    $message = "
    <html>
    <head>
      <title>Reset Password</title>
    </head>
    <body>
    <div>  
    <p>Hello $username</p>
       <p>Please click the link below to reset your password\r</p>
       <a href='https://" . $folder. "/doReset.php?resetid=$randString' >Click Here to Reset Password</a>
           
           <br />

    <p>Replies to this email will not be delivered.</p>
       </div>
    </body>
    </html>
    ";
    //manage the email's length
    $message = wordwrap($message, 140, "\r\n");
    
    // Add the reset id to the member's
    $rIdArray = array("resetID" => $randString, "username" => $username);
    $db->update($rIdArray, "Member", "username");
    
    // mail returns a boolean  indicating the success or delivery of mail
    return mail('cst211@cst.siast.sk.ca ', 'Classified: Password Reset', $message, $headers);
}

//generates  random value to be used in getToken()
function crypto_rand_secure($min, $max) {
    $range = $max - $min;
    if ($range < 0)
        return $min;
    $log = log($range, 2);
    $bytes = (int) ($log / 8) + 1;
    $bits = (int) $log + 1; // length in bits
    $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
    do {
        $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
        $rnd = $rnd & $filter; // discard irrelevant bits
    } while ($rnd >= $range);
    return $min + $rnd;
}

// renders the randomized string and stores it in the database
function getToken() {
    $token = "";
    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
    $codeAlphabet.= "0123456789";
    for ($i = 0; $i <= 6; $i++) {
        $token .= $codeAlphabet[crypto_rand_secure(0, strlen($codeAlphabet))];
    }
    return $token;
}

//retrieves the username from a query result.
function getUsername($qry) {
    $row = $qry->fetch_row();
    for ($i = 0; $i < 1; $i++) {
        $res = htmlspecialchars($row[$i]);
    }
    return $res;
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Reset</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css">
        <link type=text/css" rel="stylesheet" href="style/SecretStyles.css" />
    </head>
    <body>
        <div class="container">
            <h1>Reset your Password</h1>

            <?php
// Once the form is submitted and the email passes the validation
//check to see if the email exists in the database
// if not then display that the email could not be found
//if the email exists
// then display that the email has been sent and wait 5 seconds before redirecting to index

            if (!isset($_POST["resetForm"])) {
                loadForm(true);
            }
// The form has been submitted and it has passed validation
            else {
                if (checkEmailRegex()) {
                    // check the database to see if it exists
                    //connect to database
                    $db = new DbObject();

                    //received email value from the page
                    $email = $_POST['email'];
                    //check email in db
                    
                    $results = $db->select("username", "Member", 'email="' . $email . '"');
                    
                    //stores the username in a variable for the storing of their resetID
                    $user = getUsername($results);

                    $email_exist = $results->num_rows; //records count
                    //if returned value is more than 0, email exists
                    if ($email_exist > 0) {
                        // send an email and replace div   
                        if (sendMail($user)) {
                           
                            echo'<script > window.setTimeout(function(){window.location.href = "index.php";}, 5000);</script>';

                            echo "<p>An email has been sent, redirecting to the homepage</p>";
                        }
                    }
                    // if the email does not exist, reload the form and display an error message
                    else if ($email_exist === 0) {
                        //display that the email does not exist
                        loadForm((true));
                        echo "<span class='emFail'>This email address is not in our system!</span>";
                    }
                }
            }
            ?>

        </div>
        <div id="toHome">
            <a href="index.php" >    <img id="logo1" alt ="classified logo" src="style\class.png"style="width:100px;height:80px"/></a>
        </div>
    </body>
</html>