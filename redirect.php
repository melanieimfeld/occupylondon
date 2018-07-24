<?php
session_start();
$counter=0;
$_SESSION['test'];
//$pageWasRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0';

if(isset($_POST['tokenBool'])){
echo $_POST['user'];
$_SESSION['test']=$_SESSION['test']+1;
echo $_SESSION['test'];
//echo $counter+1;
}

echo $_SESSION['test'];

?>



<html>
<head>
    <!-- other headers -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="js/jquery.redirect.js"></script>
    <script>
     jQuery(function($){
     //OnClick testButton do a POST to a login.php with user and pasword
      $("#testButton").click(function(){
       $.redirect("redirect.php", {tokenBool: "true", password: "12345"}, "POST"); 
      });
     });
    </script>
</head>
<body>
   <button id="testButton">Test Redirect</button>
</body>
</html>