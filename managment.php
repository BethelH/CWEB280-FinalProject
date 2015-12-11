<?php
function my_autoloader($className) {
    require_once( "classes/$className.class.php" );
}

spl_autoload_register("my_autoloader");
session_start();
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
            
                if (!isset($_SESSION["loggedIn"])) {
                    echo "<p>You must be logged in to view the management page</p>";
                }
                else if(isset($_SESSION["loggedIn"]) && ($_SESSION["loggedIn"]))
                {
                    echo "<h1>Secret Pages</h1>"; 
                }
            ?>
        </div>
          <div id="toHome">
            <a href="index.php" >    <img id="logo1" alt ="classified logo" src="style\class.png"style="width:100px;height:80px"/></a>
        </div>
    </body>
</html>
