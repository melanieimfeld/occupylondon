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
 
<!--  https://stackoverflow.com/questions/15649001/bootstrap-input-field-and-dropdown-button-submit#15649153 -->
    <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"><i class="fas fa-building"></i>  Secure your lot by building</h4>
        </div>
        <div class="modal-body" id="modalbody">
            <p id="buildText"></p>
        <!-- tryin to put a dropdown in the modal. on submit, send the content to xx --> 
        <!-- <form name="searchForm" id="searchForm" method="POST" action="php/build.php"> -->
<div class="btn-group"> 
    <i class="dropdown-arrow dropdown-arrow-inverse"></i>
    <button class="btn btn-primary status">status</button>
    <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown"> <span class="caret"></span> 
    </button>
    <ul class="dropdown-menu dropdown-inverse">
        <li><a href="#fakelink">Mixed-use City in a City</a>
        </li>
        <li><a href="#fakelink">School</a>
        </li>
        <li><a href="#fakelink">discontinued</a>
        </li>
    </ul>
</div>

         <!--    <input type="hidden" name="selection" id="buildInput"> 
            <div class="dropdown">
              <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Building options
              <span class="caret"></span></button>
              <input id="search_type" name="search_type" type="hidden">
              <ul class="dropdown-menu" id="dropdownBuild">
                <li style="cursor:pointer">Mixed-use City in a City</li>
                <li style="cursor:pointer">Business Center</li>
                <li style="cursor:pointer">Hospital</li>
                <li style="cursor:pointer">School</li>
                <li style="cursor:pointer">Lido</li>
                <li style="cursor:pointer">Housing</li>
                <li style="cursor:pointer">Community Center</li>
                <li style="cursor:pointer">Neighborhood Park</li>
                <li style="cursor:pointer">Community Garden</li>
                <li style="cursor:pointer">Pop up space</li>
              </ul>
            </div> -->
        <!-- end of dropdown -->
        </div>
        <div class="modal-footer">
          <button class="btn btn-primary" data-dismiss="modal" aria-hidden="true">Cancel</button>
          <button id="allSubmitBtn" class="btn btn-primary">Build!</button>
        </div>
      </div>
      
    </div>
  </div>

    <!------------------------------- Navigation -------------------->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-bottom">
      <div class="container">
        <a class="navbar-brand" href="map.php" style="cursor:pointer"><i class="fas fa-arrow-left"></i> SPOT MORE</a>
    
        <!--------------------- these are the control buttons for drawing (new)----------------------->
        <div id="controls" style="display:none">
          <a id="startBtn" class="navbar-brand" style="cursor:pointer">start mapping</a>
          <a id="deleteBtn" class="navbar-brand" style="cursor:pointer">delete</a>
          <a id="saveBtn" class="navbar-brand" style="cursor:pointer">claim this land</a>
        </div>
        <!--------------------- all the rest in navbar ----------------------->       
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
          <ul class="navbar-nav ml-auto">
            <li class="nav-item">
              <a class="nav-link" href="rules.php">Rules</a>
              <span class="sr-only">(current)</span>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="logout.php">Logout</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <!--------------------- this is the container with land and tokens ----------------------->  
  <div class="container-fluid mx-3 mt-3" id="test">
    <div class="row">
      <div class="col-md-5" id="chart1">
          <h1>Building options</h1>
          <p>Click on your vacant lots in the map to build and secure your lot. Your building options depend on your tokencount and the area of a given lot:</p>
          <table class="table table-hover" style="width:100%">
            <tr>
              <th>Type</th>
              <th>Max. Area (sqm)</th> 
              <th>Cost (Tokens)</th>
            </tr>
            <tr>
              <td>Mixed-use city in a city</td>
              <td>30'000</td> 
              <td>50</td>
            </tr>
            <tr>
              <td>Business Center</td>
              <td>10'000</td> 
              <td>40</td>
            </tr>
            <tr>
              <td>Hospital</td>
              <td>8'000</td> 
              <td>35</td>
            </tr>
            <tr>
              <td>School</td>
              <td>4'000</td> 
              <td>30</td>
            </tr>
            <tr>
              <td>Lido</td>
              <td>3'000</td> 
              <td>25</td>
            </tr>
             <tr>
              <td>Housing</td>
              <td>2'000</td> 
              <td>20</td>
            </tr>
            <tr>
              <td>Community Center</td>
              <td>1'000</td> 
              <td>10</td>
            </tr>
            <tr>
              <td>Neighborhood Park</td>
              <td>800</td> 
              <td>8</td>
            </tr>
            <tr>
              <td>Community Garden</td>
              <td>300</td> 
              <td>3</td>
            </tr>
            <tr>
              <td>Pop-up Space</td>
              <td>200</td> 
              <td>2</td>
            </tr>
          </table>
          <svg class="chart"></svg>
          <hr>
          <div class="row">
            <div class="col-md-4"></span>Your tokens: <?php echo htmlspecialchars($_SESSION['token'])?> </div>
            <div class="col-md-4" id ="area"></div>
            <div class="col-md-4" id ="land"></div>
          </div>
        </div>
        <div class="col-md-7">
          <div id="map" class="mt-3"></div>
        </div>
      </div>
    </div>
  </div>
</div> <!--/.fluid-container-->

<script>

function stuffToResize(){
        var h_window = $(window).height();
        var h_map = h_window;
        $('#map').css('height', h_map);
}

$(window).on("resize", stuffToResize).trigger('resize'); 

var width = document.getElementById('chart1').clientWidth;
console.log('clientwidth', width);

  //counting site visits
   var counter = 0;
   var tokens = document.getElementById('tokens');

   var hStyle = {
    "stroke":true,
    "color":"#15a956",//data.rows[i].strokeColor,
    "weight":4,
    "fillOpacity":0.2,
    "opacity":1,
    "fill":true,
    "clickable":true
  }
   // var player = <?php //echo json_encode($_SESSION['number']); ?>;
   // tokens.innerHTML = '<p> you got ' + count + ' tokens and your username is '+ player + '</p>';

//----------------connect to CARTO DB and draw 2 datapoints-----------
    //add data we created from CARTO
    //create a global variable, empty
    var cartoDBpoints = null;  
    var cartoDBusername = "melanieimfeld";
    var SQLquery = "SELECT * FROM data_game";  //write the SQL query I want to use
    var playername = <?php echo json_encode($_SESSION['username']); ?>;  //get playername
    var token = <?php echo json_encode($_SESSION['token']); ?>;  
    var map = L.map('map',{ center: [51.503723,  0.057056], zoom: 12, zoomControl:true}); // Create Leaflet map object
    var myScore = {area:0, land:0}; //empty array to hold displayed scores
    var cont1 = document.getElementById('area');
    var cont2 = document.getElementById('land');
    var entry; //entry for list of buildings
    
    getScores(cont1,cont2);
    //update getScores ever 5 seconds
    // setInterval(function () {
    //   getScores(cont1,cont2);
    //   console.log('update');
    // }, 5000);

    //show controls when button 'vacant' is pressed
    function addButtons(){
      console.log(document.getElementById('controls'));
      $("#controls").show(300);
      //$.post("index-username.php", {"update": 10});
      //document.getElementById('controls').style.display = "block";
    }

    //if plot is classified as bought make opacity zero 
    function getOpacity(d) {
        return d == false ? 1 :
              d == true  ? 0.1:
                0.5;
      };

    //get CARTO selection as geoJSON and add to leaflet map
    function getGeoJSON(){
      $.getJSON("https://"+cartoDBusername+".carto.com/api/v2/sql?format=GeoJSON&q="+SQLquery, function(data){
     
        cartoDBpoints=L.geoJson(data, {
          style:hStyle,
          onEachFeature: function(feature, layer) {

            //only make markers clickable that belong to player
            if (feature.properties.current_owner == playername && feature.properties.secured == true){
//---------------------------This is the entire building selection--------------------
             layer.on('click', function (){
              $("#myModal").modal('show');

              $( function() { //problem: if draggable no more resposiveness
              $( ".modal-dialog" ).draggable();
              });

              //create list dynamically.
              var list = document.getElementById('dropdownBuild');
              $('#buildText').text("Area of this lot: " + feature.properties.area + "sqm. You can secure your lot from being bought by others by building something on it. Only the options that fulfil the area requirements are shown. Keep in mind your tokencount.");
              //var firstname = document.getElementById('firstname').value;
              // var buildings = {'Mixed-use City in a City':30000,
              //   'Business Center':1000,
              //   'Hospital': 8000,
              //   'School': 4000,
              //   'Lido': 3000, 
              //   'Housing': 2000,
              //   'Community Center': 1000,
              //   'Neighborhood Park': 800,
              //   'Community Garden': 300,
              //   'Pop up Space':200
              // };

               var buildings = {'Mixed-use City in a City':[30000,50],
                'Business Center':[10000,40],
                'Hospital': [8000,35],
                'School': [4000,30],
                'Lido': [3000,25],
                'Housing': [2000,20],
                'Community Center': [1000,10],
                'Neighborhood Park': [800,8],
                'Community Garden': [300,3],
                'Pop-up Space':[200,2]
              };

              //console.log('accces key', buildings2['Business Center'][0]);

              // for (var k in buildings){
              //   if (buildings[k][0] >= feature.properties.area){
              //     entry = document.createElement('li');
              //     entry.style.cursor = 'pointer';
              //     entry.appendChild(document.createTextNode(k));
              //     list.appendChild(entry);
              //     console.log('hello?');
              //   }
              // }

              // if (feature.properties.area > 4000) {
              //     //$("#dropdownBuild").remove();
              //     console.log('hello?');
              //     var entry = document.createElement('p');
              //     entry.appendChild(document.createTextNode('no building fits'));
              //     //list.appendChild(entry);
              // }

              //console.log('check list',list.childNodes.length);
              var selText;
              var selVal;
              var globalX = feature.properties.cartodb_id;
              var tokenVal = 1;

              $('.dropdown-inverse li > a').click(function(e){
                  var x = $('.status').text(this.innerHTML);
                  console.log("isthistext", x);
                  var yourText = $(this).text();
                  console.log("isthistext?", yourText);
              });

              // $(".dropdown-menu li").click(function(){

              //   selText = $(this).text(); //type of building
              //   selVal=buildings[selText][1]; //the value belonging to type
              //   console.log(typeof(selVal));
              //   console.log('text selected', selText);
              //   console.log('value selected', buildings[selText][1]);
              //   //$(this).parents('.btn-group').find('.dropdown-toggle').html(selText+'<span class="caret"></span>');
              //   jQuery(function($){
              //     $("#allSubmitBtn").click(function(e){
              //       if (token >= tokenVal){
              //       console.log('something submitted?');
              //       var sql ="UPDATE data_game SET usage='"+selText+"', secured="+false+" WHERE cartodb_id="+globalX;
              //       //this is the function that will submit the request to proxy. see line 170
              //       submitToProxy(sql);
              //       $.redirect("scoreoverview.php", {tokenMinusB: tokenVal}, "POST"); 
              //       $("#myModal").modal('hide');
              //       alert('you built something');
              //       //$("#dropdownBuild").children().remove();
              //       console.log('building submitted to db and tokes deducted');
              //       } else {
              //         alert('not enough tokens!');
              //       }
              //     });
              //   });

              // });

                $('#myModal').on('hidden.bs.modal', function () {
                   //$("#dropdownBuild").children().remove();
                  console.log('this is the list when closed', list);
              });

            });
          }
        layer.setStyle({
          color: feature.properties.playercolor,
          fillOpacity: getOpacity(feature.properties.secured)
        });

        }
      }).addTo(map);
    });
  }


    // var random = getRandomArbitrary(0,5);
    // console.log(random);
    var session_key = <?php echo json_encode($_SESSION['array'][$_SESSION['count']]); ?>;
    console.log('sessionkey', session_key);

    //run above function when document loads
    $(document).ready(function() {
        $("#flag").hide();
        getGeoJSON();
    });

var drawControl = new L.Control.Draw({
      position: 'topright',
      draw:false,
      edit:false     
    });

// Boolean global variable used to control visiblity
var controlOnMap = false;

// Create variable for Leaflet.draw features
var drawnItems = new L.FeatureGroup();


    // Add Tile Layer basemap
    L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}',{
      attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
      maxZoom: 18,
      subdomains:['mt0','mt1','mt2','mt3']
    }).addTo(map);

  //   // Function to run when feature is drawn on map
  // map.on('draw:created', function (e) {
  //   var layer = e.layer;
  //   drawnItems.addLayer(layer);
  //   map.addLayer(drawnItems);
  //   //dialog.dialog("open");
  //   console.log("run");
  // });


//console.log('points number2', cartoDBpoints);

var submitToProxy = function(q){
      $.post("php/callProxy.php", { // <--- Enter the path to your callProxy.php file here
        qurl:q,
        cache: false,
        timeStamp: new Date().getTime()
      }, function(data) {
        console.log(data);
        //loads the new data
        refreshLayer();
      });
    };

//if post was sent new data is loaded
function refreshLayer() {
      if (map.hasLayer(cartoDBpoints)) {
        console.log('points number3', cartoDBpoints);
        map.removeLayer(cartoDBpoints);
      };
      getGeoJSON();
    };

</script>
</body>
</html>
