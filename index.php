
<?php
session_start();
$_SESSION['count'] = 0;

//if username has been posted
if ($_POST) {
  $_SESSION['username'] = $_POST["enteredName"];
 
  //update scores
  $_SESSION['token'] = $_SESSION['token']- $_POST["variable2"];
  $_SESSION['land'] = $_SESSION['land']+$_POST["variable1"];
  $_SESSION['area'] = $_SESSION['area']+$_POST["variable3"];

  //create an array and shuffle it for each user once
  $_SESSION['array'] = range(0,109);
  shuffle($_SESSION['array']);
  header("Location: map.php");
 
} else {
  $_SESSION['username'] = "";
  $_SESSION['token']=0;
  $_SESSION['land']=0;
  $_SESSION['area']=0;

}

// if ($_SESSION['username']!=0) echo '<div id="form-submit-alert">Form Submitted!</div>';
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Intro</title>
    <link rel="stylesheet" href="css/leaflet.css" />
    <link rel="stylesheet" href="css/leaflet.draw.css" />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
  </head>
  <body>

  <div class="container" style="text-align:center">
    <div class="col-md-4 col-md-offset-4">
       <div id="header">
        <h1>Hi, please enter a name for this session</h1>
        </div>
        <form method="post">
        enter your playername: <br>
        <input type="text" name="enteredName" required><br>
        <input type="submit">
        </form>
    </div>
  </div>

    <script src="js/leaflet.js"></script>
    <script src="js/leaflet.draw.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <script>

    </script>
  </body>
</html>