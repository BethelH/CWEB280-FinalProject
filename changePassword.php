<?php
function my_autoloader($className) {
    require_once( "classes/$className.class.php" );
}

spl_autoload_register("my_autoloader");
session_start();


function checkPasswordMatch()
{
    return $_POST["newPassword1"] == $_POST["newPassword2"];
}

function checkCurrentPassword()
{
    $passwordCheck = new PasswordChecker();
    return $passwordCheck->isValid($_SESSION["username"], $_POST["currentPassword"]);
}

function loadForm($formPass)
{
     echo "<h1>Change your Password</h1>";
     $form = new HtmlForm();
     $form->renderStart("changePasswordForm","");
     $form->renderPassword("currentPassword", "Current Password: ", true, 255);
     if(!$formPass && !checkCurrentPassword())
     {
        echo "<span class='pswdMtchFail'>This is not your password. Forgot your password? <a href='ResetPassword.php'>Reset it here</a></span>";
     }
     $form->renderPassword("newPassword1", "New Password: ", true, 255);
     $form->renderPassword("newPassword2", "Again: ", true, 255);
     if(!$formPass && !checkPasswordMatch())
     {
         echo "<span class='pswdMtchFail'>does not match new password entered</span>";
     }
     $form->renderSubmitEnd("submitPasswordChange", "Change Password");
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
        
            if (!isset($_POST["submitPasswordChange"]))
            {
                loadForm(true);
            }
            else
            {
                //if the passwords do not match - or password is left blank - load the form again
                //with an error message. Send false indicating that the password check failed. 
                if(!checkPasswordMatch())
                {
                    loadForm(false);
                }
                else
                {
                    $db = new DbObject();
                    $passwordCheck = new PasswordChecker();
                    $passwordUsernamePass = $passwordCheck->isValid($_SESSION["username"], $_POST["currentPassword"]);
                    if($passwordUsernamePass)
                    {
                        $newPassword = password_hash( $_POST["newPassword1"], PASSWORD_DEFAULT );
                        $passwordArray = array("password" => $newPassword, "username" => $_SESSION["username"]);
                        $usernamePasswordChangeSuccess = $db->update($passwordArray, "Member", "username");
                        
                        if($usernamePasswordChangeSuccess == 1)
                        {
                            $_SESSION["loggedIn"] = false;
                            echo "<h2>Password succesfully changed! Please log in with your new credentials</h2>
                            <h2><a href='login.php'>Log in</a> to log in</h2>";
                        }
                    }
                    else
                    {
                        loadForm(false);
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
