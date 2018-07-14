<?php
session_start(); 
session_unset(); 
// destroy session, it will remove ALL session settings
session_destroy();
  
//redirect to login page
echo "All session variables are now removed, and the session is destroyed." ;
header("Location: index-username.php");
?>