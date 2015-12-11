<?php
function my_autoloader($className) {
    require_once("classes/$className.class.php");
}

spl_autoload_register("my_autoloader");
//start the session
session_start();

$form = new HtmlForm();
$db = new DbObject();
function checkEmailRegex($email)
{
    return (preg_match('/^\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,3}$/', $_POST["$email"]));
}
function checkEmpty()
{
    return  !empty($_POST["txtTitle"])
            && !empty($_POST["txtMessage"]) 
            && !empty($_POST["txtEmail_1"])
            && !empty($_POST["txtChallenge_1"])
            && !empty($_POST["txtAnswer_1"]);
}

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Create Secret</title>

        <script type ="text/javascript" src="//code.jquery.com/jquery-2.1.1.min.js"></script>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css">
        <link type=text/css" rel="stylesheet" href="style/SecretStyles.css" />

        <style>
            #addButton {

                background-color: black;
            }
        </style>
        <script type='text/javascript'>

            var count = 1;
            function addRecipient(id)
            {

                if (id < 7)
                {
                    var challenge = "txtChallengeQuestion_" + id;
                    var answer = "txtChallengeAnswer_" + id;
                    var email = "txtEmail_" + id;
                    var cLabel = "Challenge Question #" + id;
                    var aLabel = "Answer for " + cLabel;
                    var eLabel = "Email for recipient #" + id;
                    $("#challenge").append("<label for ='challenge'>" + cLabel + "</label><input type='text'name='challenge' id='challenge'  maxlength='140' class='form-control' required='1'>");
                    $("#answer").append("<label for ='answer'></label>" + aLabel + "<input type='text' name='answer' id='answer'  maxlength='50' class='form-control' required='1'>");
                    $("#email").append("<label for ='challenge'></label>" + eLabel + "<input type='text' name='email' id='email'   class='form-control' required='0'>");
                    if(id == 2)
                    {
                    $("#secret").append("<input type='text' name='hiddenCount' id='hiddenCount' >");
                    $("#hiddenCount").hide();
                    }
                    $("#hiddenCount").val(id);
                }
                
                else
                {
                    $("#addButton").attr("disabled", true);
                }

            }




        </script>

    </head>
    <body>
        <div class="container">
            <?php
   
                $form->renderStart("createSecret", "Secret Page Data");
                $form->renderTextbox("txtTitle", "Title for page", true, 50);
                $form->renderTextbox("txtMessage", "Secret Message (up to 500 characters)", true, 500);
                $form->renderTextbox("txtImagePath", "Image File Path (16KB image size max");
                $form->renderTextbox("txtChallengeQuestion_1", "Challenge Question", true, 140);
                
                echo "<div id = 'challenge'></div>";
                $form->renderTextbox("txtChallengeAnswer_1", "Challenge Answer", true, 50);
                echo "<div id = 'answer'></div>";
                 $form->renderTextbox("txtEmail_1", "Email:", true, 50, "", "pattern='^\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,3}$'");
                echo "<div id = 'email'></div>";
                echo"<button type='button' id='addButton' onClick='count++;addRecipient(count);' value='Add Recipients'>Add Recipients</button>";
                echo"<div id = 'secret'></div>";
                $form->renderSubmitEnd("submitSecret", "Submit","Cancel");
            ?>

        </div>
        <div id="toHome">
            <a href="index.php" >    <img id="logo1" alt ="classified logo" src="style\class.png"style="width:100px;height:80px"/></a>
        </div>
    </body>
</html>