<?php

function my_autoloader($className) {
    require_once( "classes/$className.class.php" );
}

spl_autoload_register("my_autoloader");
session_start();

function checkPasswordMatch() {
    return $_POST["newPassword1"] == $_POST["newPassword2"];
}

function loadForm($formPass) {
    echo "<h1>Reset your Password</h1>";
    $form = new HtmlForm();
    $form->renderStart("resetPasswordForm", "");
    $form->renderPassword("newPassword1", "New Password ", true, 255);
    $form->renderPassword("newPassword2", "Confirm New Password ", true, 255);
    if (!$formPass && !checkPasswordMatch()) {
        echo "<span class='pswdMtchFail'>does not match new password entered</span>";
    }
    $form->renderSubmitEnd("submitReset", "Change Password");
}

// checks to see if a valid reset id was used to access the page and then passes along the success status
function checkResetID() {
    if (isset($_GET['resetid'])) {
        $db = new DbObject();
        $resUser = $db->select("username", "Member", 'resetID="' . $_GET['resetid'] . '"');

        $username = getUsername($resUser);
        return $username;
    } else {
        return false;
    }
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
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css">
        <link type=text/css" rel="stylesheet" href="style/SecretStyles.css" />
    </head>
    <body>
        <div class="container">
            <?php
            if (!isset($_POST["submitReset"]) && checkResetID()) {
               
                    loadForm(true);
               
            } else {
//if the passwords do not match - or password is left blank - load the form again
                //with an error message. Send false indicating that the password check failed. 
                if (!checkPasswordMatch()) {
                    loadForm(false);
                }
                // if password  is a success then update the table with the new password 
                else {
                    $db = new DbObject();
                    $passwordCheck = new PasswordChecker();

                    //hash the new password and insert it into the database
                    $newPassword = password_hash($_POST["newPassword1"], PASSWORD_DEFAULT);
                    $passwordArray = array("password" => $newPassword, "username" => checkResetID());
                    $usernamePasswordChangeSuccess = $db->update($passwordArray, "Member", "username");

                    if ($usernamePasswordChangeSuccess === 1) {

                        echo "<h2>Password succesfully changed! Please log in with your new credentials</h2>
                            <h2><a href='login.php'>Click here</a> to log in</h2>";
                    } else if ($usernamePasswordChangeSuccess === 0) {
                        echo "<h1>Invalid Reset link. Rerouting to homepage</h1> ";
                        echo'<script > window.setTimeout(function(){window.location.href = "index.php";}, 3000);</script>';
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
