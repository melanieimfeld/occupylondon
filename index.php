
<?php
include 'php/cartoDBProxy.php';
ini_set('display_errors',1);

session_start();

$_SESSION['count']=0;
$_SESSION['token']=0;
$_SESSION['land']=0;
$_SESSION['area']=0;

//if anything was submitted
if ($_POST) {
  //currently, username doesn't allow quotation marks. throws error
  $name = $_POST["enteredName"];
 
  //check if user exists
  $queryURL = "SELECT playercolor,current_owner FROM data_game WHERE current_owner='$name' LIMIT 1";
  $return = goProxy($queryURL) or die('unable to connect');
  $return =  json_decode($return, true);
  //echo json_last_error_msg();
  echo var_dump($return);
  //echo $return;

  //numbers are not recognized and true is returned
  if (!in_array($name ,$return)){
    echo 'name in array';
    //echo var_dump($return);
    //assign existing color
    $_SESSION['usercolor'] = $return["rows"][0]["playercolor"];
    echo $_SESSION['usercolor'];
    // set the other session variables
    $_SESSION['username'] = $name;
    //update scores
    $_SESSION['token'] = $_SESSION['token']- $_POST["variable2"];
    $_SESSION['land'] = $_SESSION['land']+$_POST["variable1"];
    $_SESSION['area'] = $_SESSION['area']+$_POST["variable3"];
      //create an array and shuffle it for each user once
    $_SESSION['array'] = range(0,109);
    shuffle($_SESSION['array']);
    header("Location: map.php"); die();

  } else {
    //echo var_dump($return);
    echo 'not in array';
    //make new color (....that doesn't exist yet!)
    $_SESSION['usercolor'] = randCol(100,255);
    $_SESSION['username'] = $name;
    //update scores
    $_SESSION['token'] = $_SESSION['token']-$_POST["variable2"];
    $_SESSION['land'] = $_SESSION['land']+$_POST["variable1"];
    $_SESSION['area'] = $_SESSION['area']+$_POST["variable3"];
      //create an array and shuffle it for each user once
    $_SESSION['array'] = range(0,109);
    shuffle($_SESSION['array']);
    header("Location: map.php"); die();
  }
 
} else {
  //$_SESSION['username'] = $name;
  $_SESSION['token']=0;
  $_SESSION['land']=0;
  $_SESSION['area']=0;

}

//get an assigned color for user
function randCol($minVal = 0, $maxVal = 255)
{
    // Make sure the parameters will result in valid colours
    $minVal = $minVal < 0 || $minVal > 255 ? 0 : $minVal;
    $maxVal = $maxVal < 0 || $maxVal > 255 ? 255 : $maxVal;

    // Generate 3 values
    $r = mt_rand($minVal, $maxVal);
    $g = mt_rand($minVal, $maxVal);
    $b = mt_rand($minVal, $maxVal);
    
    // Return a hex colour ID string
    return sprintf('#%02X%02X%02X', $r, $g, $b);
}


// if ($_SESSION['username']!=0) echo '<div id="form-submit-alert">Form Submitted!</div>';
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Spot-a-lot</title>
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
        <!-- <input type="submit"> -->
        <button type="submit" href="map.php" class="btn btn-primary">START</button>
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