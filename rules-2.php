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

        <div class="col-md-6"><h3 class="mt-3">Acquire lots <i class="fa fa-shopping-cart" aria-hidden="true"></i></h3>If you come across a lot someone else has mapped while clicking through the images you can acquire it. The price of a lot is dynamic: It depends on how much you are willing to pay, your token count and the amount paid in a previous bid.</div>

        <div class="col-md-6"><h3 class="mt-3">Build! <i class="fas fa-building"></i></h3>Your lot will not be entirely secured from acquirement unless you decide to build something. You can choose different options <span style="background-color: #a9dabb"><a href="scoreoverview.php">here</a></span> based on your lot size and tokens available. Once the lot is occupied, no one will be able to take it from you.</div>

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


<!--    <div class="col-md-6 mt-3"><img class="img-fluid rounded float-left" src="images/ex1.jpg" alt="vacant1">
                </div>
                <div class="col-md-6 mt-3"><img class="img-fluid rounded float-left" src="images/ex1.jpg" alt="vacant1">
                </div>
                <div class="col-md-6 mt-3"><img class="img-fluid rounded float-left" src="images/ex1.jpg" alt="vacant1">
                </div>
                <div class="col-md-6 mt-3"><img class="img-fluid rounded float-left" src="images/ex1.jpg" alt="vacant1">
                </div> -->

<script>

// function stuffToResize(){
//         var h_window = $(window).height();
//         var h_map = h_window;
//         $('#map').css('height', h_map);
// }

function build(){
  var y = $('#search_type').val('store');
  console.log('what is this', y); 
  //var z = $('#searchForm').submit();
}

// $(window).on("resize", stuffToResize).trigger('resize'); 

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

    //write the SQL query I want to use
    var SQLquery = "SELECT * FROM data_game";

    var playername = <?php echo json_encode($_SESSION['username']); ?>;  //get playername
    var token = <?php echo json_encode($_SESSION['token']); ?>;  

     // Create Leaflet map object
    var map = L.map('map',{ center: [51.5310, 0.1007], zoom: 15, zoomControl:true});
    var myScore = {area:0, land:0}; //empty array to hold displayed scores

    var cont1 = document.getElementById('area');
    var cont2 = document.getElementById('land');
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

    //here, an update needs to be sent to the database
    var flagged = false;

    function addFlag(){
      //get current length of carbd dataframe (=cartodb id) and
      //update this column with current id plus 1
      if (flagged==false){
      var carto = 46;
      var sql = "UPDATE data_game SET no_falsified=no_falsified+1 WHERE cartodb_id="+carto;
      submitToProxy(sql);
        alert("you just flagged a falsely classified property");
      flagged=true;
      } else{
        alert("you can't flag this twice, sorry");
      }
      //console.log(postData(sql));
    }



    //get CARTO selection as geoJSON and add to leaflet map
    function getGeoJSON(){
      $.getJSON("https://"+cartoDBusername+".carto.com/api/v2/sql?format=GeoJSON&q="+SQLquery, function(data){
     
  

       //if plot is classified as bought make opacity zero 
      function getOpacity(d) {
        return d == false ? 1 :
              d == true  ? 0.1:
                0.5;
      };

        cartoDBpoints=L.geoJson(data, {
          style:hStyle,
          onEachFeature: function(feature, layer) {

            layer.setStyle({
              color: feature.properties.playercolor,
              fillOpacity: getOpacity(feature.properties.secured)
            });


            //only make markers clickable that belong to player
            if (feature.properties.current_owner == playername){


//---------------------------This is the entire building selection--------------------
             layer.on('click', function (){
              $("#myModal").modal('show');
              //create list dynamically.
              var list = document.getElementById('dropdownBuild');
              //var firstname = document.getElementById('firstname').value;
              var buildings = {'School': 2000, 'Hospital': 4000,'Pop-up Bar':50,'Housing':700};

              for (var k in buildings){
                if (buildings[k] >= feature.properties.area){
                  var entry = document.createElement('li');
                  entry.style.cursor = 'pointer';
                  entry.appendChild(document.createTextNode(k));
                  list.appendChild(entry);
                }
              }

              if (feature.properties.area > 4000) {
                  //$("#dropdownBuild").remove();
                  console.log('hello?');
                  var entry = document.createElement('p');
                  entry.appendChild(document.createTextNode('no building fits'));
                  //list.appendChild(entry);
              }

              console.log('check list',list.childNodes.length);

              var selText;
              var globalX = feature.properties.cartodb_id;
              var tokenVal = 5;

              $(".dropdown-menu li").click(function(){
                selText = $(this).text();
                console.log('value selected', selText);
                //$(this).parents('.btn-group').find('.dropdown-toggle').html(selText+'<span class="caret"></span>');
                jQuery(function($){
                  $("#allSubmitBtn").click(function(e){
                    if (token >= tokenVal){
                    var sql ="UPDATE data_game SET usage='"+selText+"', secured="+false+" WHERE cartodb_id="+globalX;
                    //this is the function that will submit the request to proxy. see line 170
                    submitToProxy(sql);
                    $.redirect("scoreoverview.php", {tokenMinusB: tokenVal}, "POST"); 
                    $("#myModal").modal('hide');
                    //$("#dropdownBuild").children().remove();
                    console.log('building submitted to db and tokes deducted');
                    } else {
                      alert('not enough tokens!');
                    }
                  });
                });

              });

                $('#myModal').on('hidden.bs.modal', function () {
                   $("#dropdownBuild").children().remove();
                  console.log('this is the list when closed', list);
              })

            });

//---------------------------popup container--------------------
            // var container = $('<div />');
            // var label = L.marker(layer.getBounds().getCenter());
            // var data = createList();

            // console.log('this is the list', data);

            // // Insert whatever you want into the container, using whichever approach you prefer
            // container.html("Area: "+feature.properties.area+" Used as: "+feature.properties.usage+" <input type='hidden' name='selection' id='buildInput'> <div class='dropdown'> <button class='btn btn-primary dropdown-toggle' type='button' data-toggle='dropdown'>Build something<span class='caret'></span></button><input id='search_type' name='search_type' type='hidden'><ul class='dropdown-menu' id='dropdownBuild'></ul>" );
       

            // // Insert the container into the popup
            // layer.bindPopup(data);

          }
        }
      }).addTo(map);

 
  
    });
  

 //console.log('value selected', selText);
    // map.on('popupopen', function () {
    //     console.log('test');
    //     });

  };


    // var random = getRandomArbitrary(0,5);
    // console.log(random);
    var session_key = <?php echo json_encode($_SESSION['array'][$_SESSION['count']]); ?>;
    console.log('sessionkey', session_key);

    //run above function when document loads
    $(document).ready(function() {
        $("#flag").hide();
        getGeoJSON();
        //get points
        $.getJSON("./data/points_selected.geojson",{contentType: "application/json; charset=UTF-8"},function(data){
        // add GeoJSON layer to the map once the file is loaded
        //console.log('pointsselected',data.features[session_key].geometry.coordinates[0][0]);
        //console.log('length',data.features.length);
        //console.log('session_key',session_key);
        var coords1 = data.features[session_key].geometry.coordinates[0][0];
        var coords2 = data.features[session_key].geometry.coordinates[0][1];
        //map.panTo(new L.LatLng(coords2,coords1));
        // // map.panTo(new L.LatLng(coords2));
  });
    });

//----------------this isn't working because of asynchronous!!!----------- 
//https://www.web-design-talk.co.uk/303/store-ajax-response-jquery/
    function getSession(){
      var viewport_id;
      $.getJSON("./data/points_selected.geojson",{contentType: "application/json; charset=UTF-8"},function(data){

        viewport_id = data.features[session_key].properties.id;
        //return viewport_id;
        console.log('viewportID inside', viewport_id);
        
    });
    return viewport_id;
    }


    console.log('viewportID outside', getSession());

//----------------use leaflet.draw to add custom points-----------
// Create Leaflet Draw Control for the draw tools and toolbox
// var drawControl = new L.Control.Draw({
//   draw : {
//     polygon : false,
//     polyline : false,
//     rectangle : false,
//     circle : false
//   },
//   edit : false,
//   remove: false
// });
var drawControl = new L.Control.Draw({
      position: 'topright',
      draw:false,
      edit:false     
    });

//----------------drawing polygons-----------
  poly = new L.Draw.Polygon(map, {
      allowIntersection: false,
      showArea: true,
      drawError: {
      color: '#15a956',
      timeout: 1000
    },
    // icon: new L.DivIcon({
    //   iconSize: new L.Point(10,10),
    //   className: 'leaflet-div-icon leaflet-editing-icon'
    // }),
    shapeOptions: {
      stroke: true,
      // color: '#ff0000',
      // weight: 1,
      // opacity: 0.7,
      // fill: true,
      // fillColor: null, //same as color by default
      // fillOpacity: 0.2,
      // clickable: true
    },
    guidelineDistance: 5,
  })


// Boolean global variable used to control visiblity
var controlOnMap = false;

// Create variable for Leaflet.draw features
var drawnItems = new L.FeatureGroup();


  $('#startBtn').on('click',function(){
    if(controlOnMap == true){
      map.removeControl(drawControl);
      poly.enable();
      controlOnMap = false;
      console.log('remove control if button is clicked again' + controlOnMap);
    }
    map.addControl(drawControl);
    controlOnMap = true;
    poly.enable();
    console.log("add control of button is clicked once" + controlOnMap);
    //$('#saveBtn').hide();
  });

  $('#deleteBtn').on('click',function(){
    drawnItems.clearLayers();
    poly.disable();
    //$('#saveBtn').hide();
  });

   $("#saveBtn").click(function(e){
  //CHECK IF POLYGON IS COMPLETE
    if(drawnItems.getLayers().length<1){
      window.alert('Oops, you need to map a vacant lot first.'); }
    //ELSE OPEN THE SUBMIT DIALOGUE
    else{
      map.removeControl(drawControl);
      controlOnMap = false;
      console.log("stopEdit " + controlOnMap);
      dialog.dialog("open");
      //$("#dialog").modal('show');
    }
  });

console.log('these are all the items', drawnItems);

    //disable controls
    // zoomControl:false (goes in the above paort)
    // map.dragging.disable();
    // map.touchZoom.disable();
    // map.doubleClickZoom.disable();
    // map.scrollWheelZoom.disable();
    // map.boxZoom.disable();

    // Add Tile Layer basemap
    L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}',{
      attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
      maxZoom: 18,
      subdomains:['mt0','mt1','mt2','mt3']
    }).addTo(map);

    // Your script will go here!
    // Function to run when feature is drawn on map
  map.on('draw:created', function (e) {
    var layer = e.layer;
    drawnItems.addLayer(layer);
    map.addLayer(drawnItems);
    //dialog.dialog("open");
    console.log("run");
  });

// ----------------the dialog to collect information----------------------
//   Use the jQuery UI dialog to create a dialog and set options
var dialog = $("#dialog").dialog({
  autoOpen: false,
  height: 300,
  width: 350,
  modal: true,
  position: {
    my: "center center",
    at: "center center",
    of: "#map"
  },
  buttons: {
    //setData is a function below
    "Add to Database": setData,
    Cancel: function() {
      dialog.dialog("close");
      map.removeLayer(drawnItems);
    },
  },
  close: function() {
    form[ 0 ].reset();
    console.log("Dialog closed");
  }
});

// Stops default form submission and ensures that setData or the cancel function run
var form = dialog.find("form").on("submit", function(event) {
  event.preventDefault();
});


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


var submitToProxy2 = function(q){
      $.post("php/callProxy.php", { // <--- Enter the path to your callProxy.php file here
        qurl:q,
        cache: false,
        timeStamp: new Date().getTime()
      }, function(data) {
        console.log(data);
        //loads the new data
      });
    };

var postData = function(url,data){
  if ( !url || !data ) return;
  data.cache = false;
  data.timeStamp = new Date().getTime()
  $.post(url,
    data, function(d) {
      //console.log(d);
    });
};


function timeConvert(unix){
  var a = new Date(unix * 1000);
  var year = a.getFullYear();
  var month = a.getMonth()+1; //starts from 0
  var day = a.getDate();
  var hour = a.getHours();
  var min = a.getMinutes();
  var sec = a.getSeconds();
  var time = year + '-' + month + '-' + day + ' ' + hour + ':' + min + ':' + sec ;
  return time;
};

//polygon: {"type": "Polygon","coordinates": [[ [100.0, 0.0], [101.0, 0.0], [101.0, 1.0],[100.0, 1.0], [100.0, 0.0] ] ]}
// var player = <?php //echo json_encode($_SESSION['username']); ?>;
// var player = 'hia';

//this function is called when button in 'submit' dialog is pressed.
//if this button is pressed, session variable 'land' needs to be updated.
function setData() {
  //username and description are coming from html
  var enteredUsername = <?php echo json_encode($_SESSION['username']); ?>;
  //this will record one token less?
  var token = <?php echo json_encode($_SESSION['token']); ?>;
  //purpose of vacant land
  var e = document.getElementById("usage");
  var usage = e.options[e.selectedIndex].text;
  console.log('user selected this', usage);

  //tracking the land count here. land() is defined above:
  postData( "index-username.php", {
    variable1: 1,
    variable2: 5
  });

  //number of polygon that is searched. needs updating!
  var search_poly = 10;

  // ST_SetSRID(geometry geom, integer srid);
  drawnItems.eachLayer(function(layer){
    var sql = "INSERT INTO data_game (the_geom,search_polygon,created_at,usage, player1,tokensOfPlayer) VALUES (ST_SetSRID(ST_GeomFromGeoJSON('";

    var a = layer._latlngs;
    console.log('what is a', a);
    var coords = "";
    
    console.log('latlng Arr: length: '+a.length+ " " +a);
        for (var i = 0; i < a.length; i++) {
          var lat = (a[i].lat).toFixed(4); // rid of rounding that was there for url length issue during dev
          var lng = (a[i].lng).toFixed(4); // rid of rounding that was there for url length issue during dev
          coords += '['+lng + ',' + lat+'],';
        }

    var unixTime = Math.floor(Date.now() / 1000);
    console.log(unixTime);

    //NEW
    var sql2 ='{"type":"MultiPolygon","coordinates":[[[' + coords + "]]]}'),4326),'" + search_poly + "','" +  timeConvert(unixTime) + "','" + usage + "','" + enteredUsername + "','" + token +"')";

    var pURL = sql+sql2;
    console.log(pURL)
        //this is the function that will submit the request to proxy. see line 170
    submitToProxy(pURL);
    console.log("Feature has been submitted to the Proxy");
  });

  map.removeLayer(drawnItems);
    drawnItems = new L.FeatureGroup();
    console.log("drawnItems has been cleared");
    dialog.dialog("close");
    alert("You purchased a property!");
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
