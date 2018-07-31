<?php
session_start();

if (empty($_SESSION['username'])){
   header("Location: index.php");
}

if(isset($_POST['tokenMinusB'])){ //when user bought lot. could have been on map.php page but could not turn of automatic redirection
  $_SESSION['token']=$_SESSION['token']-$_POST['tokenMinusB'];
} 

?>

<!DOCTYPE html>
<html lang="en">

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Spot-a-lot</title>

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="css/leaflet.css" />
    <link rel="stylesheet" href="css/leaflet.draw.css" />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
     <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.1/css/all.css" integrity="sha384-O8whS3fhG2OnA5Kas0Y9l3cfpmYjapjI0E4theH4iuMD+pLhbf6JI0jIMfYcK3yZ" crossorigin="anonymous">

    <script src="js/leaflet.js"></script>
<!--     <script src="js/leaflet.label.js"></script> -->
<!--     <script src="js/leaflet.draw.js"></script> -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/0.4.2/leaflet.draw.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
   <!--  <script src='https://api.mapbox.com/mapbox.js/v3.1.1/mapbox.js'></script> -->
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <script src="https://d3js.org/d3.v4.min.js"></script>
    <script src="js/scores.js"></script>
    <script src="js/jquery.redirect.js"></script>

       <!-- Bootstrap core JavaScript -->
    <!-- <script src="vendor/jquery/jquery.min.js"></script> -->
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  </head>

  <body>

    <!------------------------------- Navigation -------------------->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-bottom">
      <div class="container">
        <a class="navbar-brand" href="map.php" style="cursor:pointer"><i class="fas fa-arrow-left"></i> SPOT MORE</a>
    
        <!--------------------- all the rest in navbar ----------------------->       
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
          <ul class="navbar-nav ml-auto">
            <li class="nav-item active">
              <a class="nav-link" href="scoreoverview.php">Score overview
                <span class="sr-only">(current)</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="logout.php">Logout</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <!--------------------- this is the container with land and tokens ----------------------->  

<div class="container-fluid">
    <div class="row mx-5">
        <div class="col-md-12"><h1 class="mt-3 mx-5" align="center">The rules</h1></div>
        <div class="col-md-12"><hr></div>
        <div class="col-md-6"><h3 class="mt-3">Spot and map lots <i class="fas fa-search"></i></h3>Spot and map as many vacant lots as you can by clicking through satellite images. The star <img src="images/star.png" alt="star" style="max-width:5%; max-height:5%;"> indicates where someone surveyed vacant land. It might be possible that there is more vacant land elsewhere in the image. By <span style="background-color: #a9dabb"><a href="scoreoverview.php">clicking through the images</a></span>, you also earn tokens, your game currency.</div>

        <div class="col-md-6"><h3 class="mt-3">Acquire lots <i class="fa fa-shopping-cart" aria-hidden="true"></i></h3>If you come across a lot someone else has mapped while clicking through the images you can acquire it. The price of a lot is dynamic: It depends on how much you are willing to pay, your token count and the amount paid in a previous bid. If you bid higher, you decrease the risk of loosing it again.</div>

        <div class="col-md-6"><h3 class="mt-3">Build! <i class="fas fa-building"></i></h3>Your lot will not be entirely secured from acquirement unless you decide to build something. You can choose different options <span style="background-color: #a9dabb"><a href="scoreoverview.php">here</a></span> based on your lot size and tokens available. Once the lot is locked, the polygon will turn solid.</div>

        <div class="col-md-6"><h3 class="mt-3">Flag <i class="fas fa-flag"></i></h3>You think someone mapped a lot that is not vacant? Click on the lot to flag it. If 3 more people agree with you, the lot will be deleted from the database. You are only allowed to flag one property per session.</div>
    </div>
        
 <!--  <div class="col-md-12" align="center"> -->
  <div class="row">
        <div class="col-md-12"><h1 class="mt-3" align="center">Vacant or not?</h1></div>
        <div class="col-md-12"><hr></div>
        <div class="col-md-12">
          <div id="carouselExampleControls" class="carousel slide mx-5" data-ride="carousel" id="centerAlign">
          <div class="carousel-inner">
            <div class="carousel-item active">
              <div class="carousel-caption d-none d-md-block">
                <h1><i class="fas fa-times-circle"></i></h1>
              </div>
              <img class="d-block w-50" src="images/ex1.jpg" alt="vacant_buildingsite" style="overflow: hidden">
            </div>
            <div class="carousel-item">
              <div class="carousel-caption d-none d-md-block">
                <h1><i class="fas fa-check-circle"></i></h1>
              </div>
              <img class="d-block w-50" src="images/ex2.jpg" alt="vacant_unused" style="overflow: hidden">
            </div>
            <div class="carousel-item">
              <div class="carousel-caption d-none d-md-block">
                <h1><i class="fas fa-check-circle"></i></h1>
              </div>
              <img class="d-block w-50" src="images/ex3.jpg" alt="vacant_fenced" style="overflow: hidden">
            </div>
            <div class="carousel-item">
               <div class="carousel-caption d-none d-md-block">
                <h1><i class="fas fa-times-circle"></i></h1>
              </div>
              <img class="d-block w-50" src="images/ex4.jpg" alt="vacant_park" style="overflow: hidden">
            </div>
          </div>
            <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
              <span class="carousel-control-prev-icon" aria-hidden="true"></span>
              <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
              <span class="carousel-control-next-icon" aria-hidden="true"></span>
              <span class="sr-only">Next</span>
            </a>
          </div>
       </div>        
  </div>
<!--   </div> -->
</div> <!--/.fluid-container-->

<script>
</script>

  </body>

</html>
