<?php

function my_autoloader($className) {
    require_once( "classes/$className.class.php" );
}

spl_autoload_register("my_autoloader");
session_start();

// If the log value is set to out then the user's session should be destroyed
if (isset($_GET['log']) && ($_GET['log'] == 'out')) {
    //if the user logged out, delete any SESSION variables
    session_destroy();
    header('location:index.php');
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Classified</title>
        
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css">
        <link type=text/css" rel="stylesheet" href="style/SecretStyles.css" />
    </head>
    <body>
        <div>
     
<!--    if the user is not logged in then the description of the page 
    and the links to log in, register, create a onetime page and reset
    password will be displayed-->
  
            
            <div class="description">
                
                <div id="Welcome">
                <img src="style/class.png" alt="Classfied logo" id="cstImg" width="400px">

            </div>
            <p>  We are a site that allows you to securely share secrets over the internet! <br />
            All you need is some secret content, a challenge question and some friends! 
            </p>
             <br />
             <h3>Options</h3>
            <p>Member Benefits:</p>
            <div class="bullets">
                <li>You can create multiple pages </li>
                <li>Delete the secret page forever</li>
                <li>Send the page to up to 3 friends</li>
                <li>See which of your friends have seen your site</li>
            </div>
            
            <br />
            <p> Visitor Features</p>
            <div class="bullets">
                <li>You can create a onetime secret page </li>
                <li>Send the page to your most trusted friend!</li>
            </div>
            
            <div>
            <div id="Nav">
            
                <?php
                if (!isset($_SESSION["loggedIn"]) || !$_SESSION["loggedIn"]) 
                {
                echo "<h2><a href='login.php'>Click here</a> to log in</h2>
                <h2>Wanna join? <a href='register.php'>click here</a> to become a member</h2>
                <h2>You could also <a href='CreateSecret.php'>click here</a> to create a one time secret page</h2>
                <h2>Forgot Your Password? <a href='ResetPassword.php'>click here</a> to reset your password</h2>";
                //If the user is logged in then the following content will be displayed\
                } 
                else if(isset($_SESSION["loggedIn"]) && $_SESSION["loggedIn"])
                {
                    echo 
                    "<h2>To manage your secretness <a href='managment.php'>click here</a> to go to the managment page</h2>
                     <h2>Need to be more secret? <a href='changePassword.php'>click here</a> to change your password</h2>
                     <h2>To access all your secret needs <a href='CreateSecret.php'>click here</a> to create a secret page</h2>
                     <h2>Need your fill of secrets? <a href='rssfeed.php'>click here</a> to get to your rssfeed</h2>
                     <h2> <a href='?log=out' class='button'>Logout</a> ";
                }
                ?>
            </div> 
        </div>

    </body>
</html>
