<?php
session_start();

$pageWasRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0';

//110 is length of points selected
if($pageWasRefreshed) {
    $_SESSION['token']++;
    //echo $_SESSION['array'][$_SESSION['count']];
    $searchPolys = 110;
    if ($_SESSION['count']<5){
    $_SESSION['count']++;
    } else {
    $_SESSION['count']=0;
    }
  }

if(isset($_POST['submit'])) { // increasing
    $_SESSION['land']++; // no need for extra variable (preincrement to echo immediately)
}
  //echo sizeof($_SESSION['array']);
  //echo $_SESSION['land'];
?>

<!DOCTYPE html>
<html lang="en">

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Settlers of London</title>

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="css/leaflet.css" />
    <link rel="stylesheet" href="css/leaflet.draw.css" />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">

    <script src="js/leaflet.js"></script>
    <script src="js/leaflet.draw.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
   <!--  <script src='https://api.mapbox.com/mapbox.js/v3.1.1/mapbox.js'></script> -->
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

       <!-- Bootstrap core JavaScript -->
    <!-- <script src="vendor/jquery/jquery.min.js"></script> -->
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  </head>

  <body>
    <!-- -----------------the dialog element--------------------- -->
    <div id="dialog" title="Point Information">     
      <form>
        <fieldset style="border: none;">
          <ul style="list-style-type: none; padding-left: 0px">
         <!--    <li><label for="username">Your Name</label></li>
            <li><input type="text" name="username" id="username" placeholder="Enter your name" class="text ui-widget-content ui-corner-all"></li>

            <li><label for="description">About this Point</label></li>
            <li><input type="text" name="description" id="description" placeholder="Description for this point" class="text ui-widget-content ui-corner-all"></li> -->

            <li><label for="usage">What would you build here?</label></li>
            <li><select for="usage" id="usage">
            <option value="Housing" class="text ui-widget-content ui-corner-all">Affordable Housing</option>
            <option value="Retail" class="text ui-widget-content ui-corner-all">A shopping temple</option>
            <option value="Home" class="text ui-widget-content ui-corner-all">My own home</option>
            <option value="Garden" class="text ui-widget-content ui-corner-all">A community garden</option>
            <option value="Bar" class="text ui-widget-content ui-corner-all">A pop up bar</option>
            <option value="School" class="text ui-widget-content ui-corner-all">A school</option>
            <option value="other" class="text ui-widget-content ui-corner-all">I have another idea</option>
            </select></li>

          </ul>
          <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
        </fieldset>
      </form>
    </div>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-bottom">
      <div class="container">
        <a class="navbar-brand" onclick="addButtons()" value="addbuttons" style="cursor:pointer">VACANT</a>
        <a class="navbar-brand" onclick="window.location.reload()" style="cursor:pointer">NEXT</a>
        <a class="navbar-brand" href="#">FLAG</a>

        <!-- <a class="navbar-brand" href="?inc=TRUE">Increment</a> -->

<!--------------------- these are the drawing buttons ----------------------->
        <div id="controls" style="display:none">
        <a class="navbar-brand" onclick="startEdits()" value="Click to Start Editing" style="cursor:pointer">start editing</a>
        <a class="navbar-brand" onclick="stopEdits()" value="Stop Your Editing Session" style="cursor:pointer">stop editing</a>
        </div>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
          <ul class="navbar-nav ml-auto">
            <li class="nav-item active">
              <a class="nav-link" href="#">Score overview
                <span class="sr-only">(current)</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">About</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="logout.php" name="logout">Logout</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  
      <div class="container-fluid" style="pointer-events: none">
        <div class="row">
          <div class="col-md-12">
            <h1 class="mt-5">DO YOU SEE ANY VACANT LAND?</h1>
          </div>
          <div class="col-md-2">Tokens: <?php echo $_SESSION['token']?></div>
          <div class="col-md-10" id ="land">Land: <?php echo $_SESSION['land']?></div>
        </div>
      </div>
    <!-- Page Content -->
 <div id="map"></div>


</div><!--/.fluid-container-->

<script>
  //counting site visits
   var count = 10;
   var tokens = document.getElementById('tokens');
   // var player = <?php //echo json_encode($_SESSION['number']); ?>;
   // tokens.innerHTML = '<p> you got ' + count + ' tokens and your username is '+ player + '</p>';

//----------------connect to CARTO DB and draw 2 datapoints-----------
    //add data we created from CARTO
    //create a global variable, empty
    var cartoDBpoints = null;  

    var cartoDBusername = "melanieimfeld";

    //write the SQL query I want to use
    var SQLquery = "SELECT * FROM data_game";


    function addButtons(){
      console.log(document.getElementById('controls'));
      $("#controls").show(300);
      $.post("index-username.php", {"update": 10});
      //document.getElementById('controls').style.display = "block";
    }

    //get CARTO selection as geoJSON and add to leaflet map
    function getGeoJSON(){
      $.getJSON("https://"+cartoDBusername+".carto.com/api/v2/sql?format=GeoJSON&q="+SQLquery, function(data){
        cartoDBpoints=L.geoJson(data,{
          pointToLayer: function(feature, latlng){
            var marker = L.marker(latlng);
            marker.bindPopup('' + feature.properties.usage + ' <br> submitted by ' + feature.properties.player1 + ' <br> '+feature.properties.no_falsified+' flagged this as false'+'');
            return marker;
          }
        }).addTo(map);
      });
    };


    function getRandomArbitrary(min, max) {
    return Math.floor(Math.random() * (max - min) + min);
    }


    // var random = getRandomArbitrary(0,5);
    // console.log(random);
    var session_key = <?php echo json_encode($_SESSION['array'][$_SESSION['count']]); ?>;
    console.log('sessionkey', session_key);

    //run above function when document loads
    $(document).ready(function() {
        getGeoJSON();
        //get points
        $.getJSON("./data/points_selected.geojson",{contentType: "application/json; charset=UTF-8"},function(data){
        // add GeoJSON layer to the map once the file is loaded
        //console.log('pointsselected',data.features[session_key].geometry.coordinates[0][0]);
        //console.log('length',data.features.length);
        //console.log('session_key',session_key);
        var coords1 = data.features[session_key].geometry.coordinates[0][0];
        var coords2 = data.features[session_key].geometry.coordinates[0][1];
        map.panTo(new L.LatLng(coords2,coords1));
        // // map.panTo(new L.LatLng(coords2));
  });
    });

    // console.log('doesnt change', coords2);

//----------------use leaflet.draw to add custom points-----------
// Create Leaflet Draw Control for the draw tools and toolbox
var drawControl = new L.Control.Draw({
  draw : {
    polygon : false,
    polyline : false,
    rectangle : false,
    circle : false
  },
  edit : false,
  remove: false
});



// Boolean global variable used to control visiblity
var controlOnMap = false;

// Create variable for Leaflet.draw features
var drawnItems = new L.FeatureGroup();


// Function to add the draw control to the map to start editing
  function startEdits(){
    if(controlOnMap == true){
      map.removeControl(drawControl);
      controlOnMap = false;
      console.log('when does this situation come?' + controlOnMap);
    }
    map.addControl(drawControl);
    controlOnMap = true;
    console.log("startEdit " + controlOnMap);
  };

  // Function to remove the draw control from the map
  function stopEdits(){
    map.removeControl(drawControl);
    controlOnMap = false;
    console.log("stopEdit " + controlOnMap);
    window.location.reload();
    };


    // Create Leaflet map object
    var map = L.map('map',{ center: [51.51, -0.10], zoom: 22,zoomControl:false});

    //disable controls
    // zoomControl:false (goes in the above paort)
    map.dragging.disable();
    map.touchZoom.disable();
    map.doubleClickZoom.disable();
    map.scrollWheelZoom.disable();
    map.boxZoom.disable();

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
    dialog.dialog("open");
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

// //retrieves IP address
// $.getJSON('http://gd.geobytes.com/GetCityDetails?callback=?', function(data) {
//   console.log(data.geobytesipaddress);
//     // console.log(JSON.stringify(data, null, 2));
// });

//console.log('points number2', cartoDBpoints);

var submitToProxy = function(q){
      $.post("php/callProxy.php", { // <--- Enter the path to your callProxy.php file here
        qurl:q,
        cache: false,
        timeStamp: new Date().getTime()
      }, function(data) {
        console.log(data);
        refreshLayer();
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
}

//polygon: {"type": "Polygon","coordinates": [[ [100.0, 0.0], [101.0, 0.0], [101.0, 1.0],[100.0, 1.0], [100.0, 0.0] ] ]}
//var player = <?php //echo json_encode($_SESSION['username']); ?>;
var player = 'hia';

function setData() {
  //username and description are coming from html
  var enteredUsername = player;
  //purpose of vacant land
  var e = document.getElementById("usage");
  var usage = e.options[e.selectedIndex].text;
  console.log('user selected this', usage);

  //polygon search
  var search_poly = 10;


  // ST_SetSRID(geometry geom, integer srid);
  drawnItems.eachLayer(function(layer){
    var sql = "INSERT INTO data_game (the_geom,search_polygon,created_at,usage, player1, latitude,longitude) VALUES (ST_SetSRID(ST_GeomFromGeoJSON('";
    var a = layer.getLatLng();
    var unixTime = Math.floor(Date.now() / 1000);
    console.log(unixTime);
    var sql2 ='{"type":"Point","coordinates":[' + a.lng + "," + a.lat + "]}'),4326),'" + search_poly + "','" +  timeConvert(unixTime) + "','" + usage + "','" + enteredUsername + "','" + a.lat + "','" + a.lng +"')";
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
}


 function refreshLayer() {
      if (map.hasLayer(cartoDBPoints)) {
        console.log('points number3', cartoDBpoints);
        map.removeLayer(cartoDBPoints);
      };
      getGeoJSON();
    };
</script>

  </body>

</html>
