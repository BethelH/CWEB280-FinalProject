<?php

function my_autoloader($className) {
    require_once( "classes/$className.class.php" );
}

spl_autoload_register("my_autoloader");
session_start();

$db = new DbObject();
 if (!isset($_SERVER["HTTPS"]) || !$_SERVER["HTTPS"]) {
    //redirect to secure
    header("HTTP/1.1 301 Moved Permanently");
 
    header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
    exit();  
}

//if they are already logged in, redirect to home page
if (isset($_SESSION["loggedIn"]) && $_SESSION["loggedIn"]) {
    header("Location: home.php");
}
//check for a valid username and password if submitted
if (isset($_POST["submitLogin"])) {
    $passwordCheck = new PasswordChecker();

    // check the posted username and password 
    if (!empty($_POST["username"])) {

        $_POST["submitLogin"] = $passwordCheck->isValid($_POST["username"], $_POST["password"]);

        //  regenerate the session id
        session_regenerate_id(true);
        //if the login is valid redirect to home page
        if ($_POST["submitLogin"]) {
            $_SESSION["loggedIn"] = true;
            $_SESSION["username"] = $_POST["username"];
            header("Location: home.php");
        }
        //display error message if wrong username or password
        else {
            echo "<div>Invalid username or password</div>";
        }
    }
}
$form = new HtmlForm();
?>

<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css">
        <link type=text/css" rel="stylesheet" href="style/SecretStyles.css" />
        <title>Login</title>
    </head>
    <body>
        <div class="container">
            <?php
            $form->renderStart("loginForm", "Enter your username and password");
            $form->renderTextbox("username", "Username", true);
            $form->renderPassword("password", "Password", true);
            $form->renderSubmitEnd("submitLogin", "Submit");
?>
            <div id="linking">
                <p> Don't have an account?<a href="register.php" > Click Here</a> to register!</a></p>
                <p>Forgot Your Password? <a href="ResetPassword.php">click here</a> to reset your password</p>
            </div>
        </div>
        <div id="toExplore">
            //Insert Image Link Here
        </div>
    </body>
</html>
