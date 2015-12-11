<?php
function my_autoloader( $className )
{
    require_once("classes/$className.class.php");
}
spl_autoload_register( "my_autoloader" );
//start the session
session_start();

//the user must be using https to register
if(!isset($_SERVER["HTTPS"]) || !$_SERVER["HTTPS"])
{
    //redirect the user to a secure version of teh requested page - SSL (Secure Socket Layer)
    header("HTTP/1.1 301 Moved Permanently");
    
    //now tell the browser where the new location is
    //the new location is simple https:// instead of http://
    header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
    exit(); //tell php to stop all other execution of any following code 
}

$db = new DbObject();
function checkForEmptyFields()
{
    return !empty($_POST["txtPassword"])
            || !empty($_POST["txtUsername"]) || !empty($_POST["txtEmail"]) 
            || !empty($_POST["txtQst1"]) || !empty($_POST["txtQst1Answer"]) 
            || !empty($_POST["txtQst2"]) || !empty($_POST["txtQst2Answer"]);
}

function checkPasswordMatch()
{
    return $_POST["txtPassword"] == $_POST["txtRetypePassword"];
}

function checkUsernameRegex()
{
    return (preg_match('/^[A-Za-z0-9\_]{3,25}$/', $_POST["txtUsername"]));
}

function checkEmailRegex()
{
    return (preg_match('/^\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,3}$/', $_POST["txtEmail"]));
}

function checkChallengeQuestions()
{
    return $_POST["txtQst1"] != $_POST["txtQst2"];
}

//load the form - pass in weather or not the passwords entered match.
function loadForm($formPass)
{
    //note: there are echoed divs here to help render error messages
    //if the passwords do not match. The way error checking is done
    //by renderTextBox does not allow for other error messages otherwise.
    //I needed to improvise here - seems to work.
    $form = new HtmlForm();

    $form->renderStart("frmRegisterForm", "Register", true);
    echo "<div >";
    $form->renderTextbox("txtUsername", "Username:", true, 64, "", "pattern='^[A-Za-z0-9\_]{3,25}$'");
    echo"<span id = 'pWord'></span>";
    echo "</div>";
    echo "<div>";
    $form->renderTextbox("txtEmail", "Email:", true, 50, "", "pattern='^\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,3}$'");
    echo "</div>";
    echo "<div>";
    $form->renderPassword("txtPassword", "Password:", true, 255);
    echo "</div>";
    //if the passwords entered match
    if($formPass)
    {
          echo "<div>";
          $form->renderPassword("txtRetypePassword", "Retype Password:", true, 255);
          echo "</div>";
    }
    //if the passwords entered do not match
    else if(!$formPass && !checkPasswordMatch())
    {
          echo "<div>";
          $form->renderPassword("txtRetypePassword", "Retype Password:", true);
          if(!empty($_POST["txtRetypePassword"]))
          {
                echo "<span class='pswdMtchFail'>does not match password entered</span>";
                echo "</div>";
          }
    }
    else
    {
         $form->renderPassword("txtRetypePassword", "Retype Password:", true);
    }
    if($formPass)
    {
        echo "<div>";
        $form->renderTextbox("txtQst1", "Enter a challenge question: ", true, 255, "", "");
        echo "</div>";
        echo "<div>";
        $form->renderTextbox("txtQst1Answer", "Answer: ", true, 255, "", "");
        echo "</div>";
        echo "<div>";
        $form->renderTextbox("txtQst2", "Enter another challenge question: ", true, 255, "", "");
        echo "</div>";
        echo "<div>";
        $form->renderTextbox("txtQst2Answer", "Answer: ", true, 255, "", "");
        echo "</div>";

    }
    else if(!$formPass && !checkChallengeQuestions())
    {
        echo "<div>";
        $form->renderTextbox("txtQst1", "Enter a challenge question: ", true, 255, "", "");
        echo "</div>";
        echo "<div>";
        $form->renderTextbox("txtQst1Answer", "Answer: ", true, 255, "", "");
        echo "</div>";
        echo "<div>";
        $form->renderTextbox("txtQst2", "Enter another challenge question: ", true, 255, "", "");
            echo "<span class='QstMtchFail'>Your challenge questions cannot be the same</span>";
            echo "</div>";
        echo "<div>";
        $form->renderTextbox("txtQst2Answer", "Answer: ", true, 255, "", "");
        echo "</div>";
    }
    else if(!$formPass && checkChallengeQuestions())
    {
        echo "<div>";
        $form->renderTextbox("txtQst1", "Enter a challenge question: ", true, 255, "", "");
        echo "</div>";
        echo "<div>";
        $form->renderTextbox("txtQst1Answer", "Answer: ", true, 255, "", "");
        echo "</div>";
        echo "<div>";
        $form->renderTextbox("txtQst2", "Enter another challenge question: ", true, 255, "", "");
        echo "</div>";
        echo "<div>";
        $form->renderTextbox("txtQst2Answer", "Answer: ", true, 255, "", "");
        echo "</div>";
    }
    $form->renderSubmitEnd("subRegisterSubmit", "Register");
}

//check to see if the form was submitted
if (isset($_POST["subRegisterSubmit"]))
{
    //if the passwords do not match - or password is left blank - load the form again
    //with an error message. Send false indicating that the password check failed. 
    if(!checkUsernameRegex() || !checkChallengeQuestions() ||  !checkEmailRegex() 
            || !checkForEmptyFields() || !checkPasswordMatch())
    {
        loadForm(false);
    }
    //otherwise passwords matched. Register the user by adding the information
    //enetered to the database and setting the session to loggedin = true.
    else
    {
        //load the form again
        loadForm(true);
        $passwordChecker = new PasswordChecker();
        $user = strip_tags( $_POST["txtUsername"] );
        $email = strip_tags( $_POST["txtEmail"] );
        $pwd = strip_tags($_POST["txtPassword"]);
        $qst1 = strip_tags($_POST["txtQst1"]);
        $qst1Answer = strip_tags($_POST["txtQst1Answer"]);
        $qst2 = strip_tags($_POST["txtQst2"]);
        $qst2Answer = strip_tags($_POST["txtQst2Answer"]);
        
        $success = $passwordChecker->addUser( $user, $pwd, $email, $qst1, $qst1Answer, $qst2, $qst2Answer);
        if ( $success )
        {
            $_SESSION["loggedIn"] = true;
            $_SESSION["username"] = $user;
        }
        //the only reason this form can fail is if the add user does not succeed.
        //this is because the username entered already matches another username
        //(primary key). Print a message saying that the username already exists
        //and get them to try again.
        else
        {
            echo "<p>Failed to add $user to the website</p>";
            echo "<p>The username $user already exists!</p>";
            echo "<p>Please enter a new username</p>";
        }
           
    }
}

//If the visitor is already logged in, they don't need to register 
//for an account so they are redirected to the index page.
//Also if the user registered correctly - they are reddirected to index.php
if(isset($_SESSION["loggedIn"]) && $_SESSION["loggedIn"])
{
    echo "<script language='JavaScript'> window.location='index.php' </script>";
}

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Register</title>
      
        <script type ="text/javascript" src="//code.jquery.com/jquery-2.1.1.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css">
         <link type=text/css" rel="stylesheet" href="style/SecretStyles.css" />

        <script type='text/javascript'>
            $(document).ready(function(){
            $("#txtUsername").change(function(){
                 $("#pWord").html("checking...");
             
 
            var username=$("#txtUsername").val();
 
              $.ajax({
                    type:"post",
                    url:"checkUsername.php",
                    data:"username="+username,
                        success:function(data){
                        if(data==0){
                             $("#pWord").html("Username available");
                        }
                        else{
                            $("#pWord").html("Username already exists");
                        }
                    }
                 });
 
            });
 
         });
        </script>
    </head>
    <body>
        <div class="container">
            <?php
            //check to see if the form was not yet submited.
            //load the form for the first time.
            if (!isset($_POST["subRegisterSubmit"]))
            {
                loadForm(true);
                echo"<h3>Already have an account?</h3> 
                     <p><a href='login.php'>Click here</a> to log in</p>";
            }
            ?>
        </div>

        </div>
            <div id="toHome">
            <a href="index.php" >    <img id="logo1" alt ="classified logo" src="style\class.png"style="width:100px;height:80px"/></a>
        </div>
    </body>
</html>