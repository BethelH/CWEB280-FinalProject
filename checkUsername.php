<?php
function my_autoloader( $className )
{
    require_once( "classes/$className.class.php" );
}

spl_autoload_register( "my_autoloader" );

if(isset($_POST["username"]))
{
  //connect to database
  $db = new DbObject();
  
  //received username value from registration page
  $username = $_POST['username'];  
  //check username in db
  $results = $db->select("username", "Member", "username='$username'");
 // var_dump($results);
 
  $username_exist = $results->num_rows; //records count
  
  //if returned value is more than 0, username is not available
      echo $username_exist;

}


