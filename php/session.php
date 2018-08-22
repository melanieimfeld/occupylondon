<?php

//if username was submitted
if (isset($_POST["name"])) {
  //echo "username was entered"
  //currently, username doesn't allow quotation marks. throws error

  $_SESSION["username"]=iconv("ASCII" , "UTF-8//IGNORE" ,$_POST["name"]);
  $name = $_POST["name"];

  echo mb_detect_encoding ($_SESSION["username"]);
  //check if user exists
  $queryURL = "SELECT playercolor,current_owner FROM data_game WHERE current_owner='$name' LIMIT 1";
  $return = goProxy($queryURL) or die('unable to connect');
  $return =  json_decode($return, true);

  //numbers are not recognized and true is returned
  if (!in_array($name ,$return)){
    echo 'name in array ';
    //assign existing color
    $_SESSION['usercolor'] = $return["rows"][0]["playercolor"];
    echo " oldcolor".$_SESSION['usercolor'];

  } else {
    echo 'not in array ';
    //make new color (....that doesn't exist yet!)
    $_SESSION['usercolor'] = "#9091ED";
    echo $_SESSION['usercolor'];
    // if(!in_array($_SESSION['usercolor'],$return)){ //extra: make sure that color is only used one
    //   echo ' color already exists';
    $_SESSION['usercolor'] = randCol(80,255);
    // }
  }
  $_SESSION['array'] = range(0,99); //size of lots that need checking
  shuffle($_SESSION['array']);
  header("Location: map.php"); die();
}

//get an assigned color for each user
function randCol($minVal = 0, $maxVal = 255){
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


$NoSearchPolys =112; //total images in the game

//if no username was defined send user back to login page
if (empty($_SESSION['username'])){
  header("Location: index.php");
}

//---------------all tokenupdates-----------------
if(isset($_POST['tokenBool'])){ //user clicked 'spot more'
  $_SESSION['token']=$_SESSION['token']+1;
  //echo $_SESSION['token'];
if ($_SESSION['count']< $NoSearchPolys){
  $_SESSION['count']++;
} else {
  $_SESSION['count']=0;
}
//echo $counter+1;
} elseif(isset($_POST['tokenMinus'])){ //user acquired another lot or bought something
  //echo 'Session var before operation '.$_SESSION['token'];
  $_SESSION['token']=$_SESSION['token']-$_POST['tokenMinus'];
  //echo 'Session var after operation '.$_SESSION['token'];
  //echo 'bid '.$_POST['tokenMinus'];
} elseif (!isset($_SESSION['token'])) { //this should only happen when user just entered the game
    $_SESSION['token']=0;
    $_SESSION['count']=0;
    //echo 'else statement, reason why token turns zero?'.$_SESSION['token']; 
}


?>
