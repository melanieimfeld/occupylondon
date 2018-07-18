<?php
session_start();

$pageWasRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0';
$NoSearchPolys =5;

//110 is length of points selected
if($pageWasRefreshed) {
  $_SESSION['token']++;
    //echo $_SESSION['array'][$_SESSION['count']];
  if ($_SESSION['count']< $NoSearchPolys){
    $_SESSION['count']++;
  } else {
    $_SESSION['count']=0;
  }
}

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
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet"/>

  <link rel="stylesheet" href="css/leaflet.css" />
  <link rel="stylesheet" href="css/leaflet.draw.css" />
  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css"/>
  <link rel="stylesheet" type="text/css" href="css/style.css"/>

  <script src="js/leaflet.js"></script>
  <!--     <script src="js/leaflet.draw.js"></script> -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/0.4.2/leaflet.draw.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
  <!--  <script src='https://api.mapbox.com/mapbox.js/v3.1.1/mapbox.js'></script> -->
  <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

  <!-- Bootstrap core JavaScript -->
  <!-- <script src="vendor/jquery/jquery.min.js"></script> -->
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

</head>

<body>
  <!-- -----------------the dialog element for submission--------------------- -->
  <div id="dialog" title="Property Information" style="display:none">     
    <form>
      <fieldset style="border: none;">
        <ul style="list-style-type: none; padding-left: 0px">
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

    <!-- -----------------the dialog element for acquiring--------------------- -->
    <div id="spinnerControls" style="display:none">
      <label id="spinnerLabel" for="spinner"></label>
      <input id="spinner" type="number">
      <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
    </div>

    <!------------------------------- Navigation -------------------->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-bottom">
      <div class="container">
        <a class="navbar-brand" onclick="addButtons()" value="addbuttons" style="cursor:pointer">VACANT</a>
        <a class="navbar-brand" onclick="window.location.reload()" style="cursor:pointer">NEXT</a>
        <a id='flag' class="navbar-brand" style="display:none; cursor:pointer">FLAG</a>
        <a id='acquire' class="navbar-brand" style="display:none; cursor:pointer">ACQUIRE ME</a>

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
            <li class="nav-item active">
              <a class="nav-link" href="scoreoverview.php">Score overview
                <span class="sr-only">(current)</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">About</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="logout.php">Logout</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <!--------------------- this is the container with land and tokens ----------------------->  
    <div class="container-fluid" style="pointer-events: none">
      <div class="row">
        <div class="col-md-12">
          <h1 class="mt-5"> <?php echo json_encode($_SESSION['username'])?>, DO YOU SEE ANY VACANT LAND?</h1>
        </div>
        <div class="col-md-2"> Tokens: <?php echo $_SESSION['token']?> </div>
        <div class="col-md-2" id ="land"> Pioneered Land: <?php echo $_SESSION['land']?> </div>
        <div class="col-md-2" id ="area"> Area: <?php echo $_SESSION['area']?> </div>
        <div class="col-md-4" id ="flags">i</div>
      </div>
    </div>
    <!-- Page Content -->
    <div id="map"></div>


  </div> <!--/.fluid-container-->

  <script>
  //counting site visits
  var counter = 0;

  var hStyle = {
    "stroke":true,
    "color":"#15a956",//data.rows[i].strokeColor,
    "weight":4,
    "fillOpacity":0.2,
    "opacity":1,
    "fill":true,
    "clickable":true
  }

//----------------connect to CARTO DB and draw 2 datapoints-----------
    //add data we created from CARTO
    //create a global variable, empty
    var cartoDBpoints = null;  
    var cartoDBusername = "melanieimfeld";
    //get playername
    var playername = <?php echo json_encode($_SESSION['username']); ?>;
    //write the SQL query I want to use
    var SQLquery = "SELECT * FROM data_game";
    //boolean to check if flagging already occurred or not
    var flagged = false;
    // Create Leaflet map object
    var map = L.map('map',{ center: [51.51, -0.10], zoom: 22, zoomControl:false});
    //get tokens
    var token = <?php echo json_encode($_SESSION['token']); ?>;
    var session_key = <?php echo json_encode($_SESSION['array'][$_SESSION['count']]); ?>;

    //show controls when button 'vacant' is pressed
    function addButtons(){
      console.log(document.getElementById('controls'));
      $("#controls").show(300);
      //$.post("index-username.php", {"update": 10});
      //document.getElementById('controls').style.display = "block";
    }

        

    function getColor(d) {
      return d == 'Affordable Housing' ? '#d73027' :
      d == 'A pop up bar'  ? '#f46d43' :
      d == 'My own home'  ? '#fdae61' :
      d == 'A shopping temple'  ? '#fee090' :
      d == 'A grocery store'  ? '#74add1' :
      d == 'A school'  ? '#4575b4' :
      d == 'A community garden'  ? '#4d9221' :
      d == 'I have another idea'  ? '#a7b000' :
      '#f5f635';
    }

    function style(feature) {
        return {
          'color': getColor(feature.properties.usage)
        };
      }


//------------------------ LOAD ALL LAND ---------------------
    //get CARTO selection as geoJSON and add to leaflet map
    function getGeoJSON(){
      $.getJSON("https://"+cartoDBusername+".carto.com/api/v2/sql?format=GeoJSON&q="+SQLquery, function(data){

  


         // //this is not correct yet. just takes any flag?
         //    var flags = 0;
         //    if (feature.properties.player1 == playername){
         //      flags ++
         //      //flags = flags + feature.properties.no_falsified
         //    }
         //    console.log('these are the total flags', flags);
 

        //if polygons:
        //http://leaflet.github.io/Leaflet.label/
        cartoDBpoints=L.geoJson(data, {
          style:hStyle,
          onEachFeature: function(feature, layer) {
            $('#flags').text('Flags: '+ flags);
            
            //this happens on each feature when flag is clicked
            layer.on('click', function () {
              $("#flag").show(300);
              $("#acquire").show(300);
              console.log('show cartodb id', feature.properties.cartodb_id);

              var globalX = feature.properties.cartodb_id;

              // add a flag to a property when flag is clicked
              $('#flag').click(function addFlag(){
                if (flagged==false){
                //here again. store a $.getjson command in variable. htf?
                //var globalX = 46;
                var update = "UPDATE data_game SET no_falsified=no_falsified+1 WHERE cartodb_id="+globalX;
                submitToProxy2(update);
                console.log('show update', update);
                flagged=true;
                alert("you just flagged a falsely classified property");
                console.log('id submitted', globalX);
                
              } else{
                alert("you can't flag this twice, sorry");
              }
            })

              //acquire property
              $('#acquire').click(function acquire(){
                //pop up dialog how much do you want to bid? you need to bid at least 1 token. the more you bid, the less likely someone will take over your property.
                 var currentBid = feature.properties.bid_for + 1;

                document.getElementById('spinnerLabel').innerHTML = "If you want to acquire this land, you need your minimum bid is " + currentBid + " ."; 

                function submitPurchase() {
                var area = feature.properties.area;
                var bid = document.getElementById("spinner").value;
                //minimum value. minimum value equals area/10
                //var bid = 30;
                
                //var spinner = e.options[e.selectedIndex].text;
                console.log('current value', currentBid);

                //conditions: larger than token size. larger than mimium amount. non negative number.
                if (bid<=token && bid>=currentBid){
                  var update = "UPDATE data_game SET bought_by="+ "'"+ playername +"'"+ ", bid_for="+ bid + " WHERE cartodb_id="+globalX;
                  submitToProxy2(update);
                  console.log('this was submitted upon purchase', update);
                  console.log('tokencount', bid);

                  //update your area count and token count:
                  postData( "index.php", {
                    variable2: bid,
                    variable3: area,
                    enteredName: playername
                  });

                } else {
                  alert('your bid is invalid');
                }

                dialog2.dialog("close");

              }

              var dialog2 = $("#spinnerControls").dialog({
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
                    "Acquire": submitPurchase,
                    Cancel: function() {
                      dialog2.dialog("close");
                      //map.removeLayer(drawnItems);
                    }
                  }
                });

              dialog2.dialog('open');

            })

            });

            layer.setStyle({
              color: getColor(feature.properties.usage)
            });

            var label = L.marker(layer.getBounds().getCenter());
            layer.bindPopup('<b>Discovered by:</b> ' + feature.properties.player1 + '<br> <b>Area:</b> ' + feature.properties.area + '<br><b>Acquired by:</b> ' + feature.properties.bought_by + '<br><b>Bid for:</b> '+ feature.properties.bid_for + '<br> '+feature.properties.no_falsified+' flagged this as false'+'');
          }
        }).addTo(map);

      });
};


function getRandomArbitrary(min, max) {
  return Math.floor(Math.random() * (max - min) + min);
}

console.log('this will be undefined', cartoDBpoints);
console.log('sessionkey', session_key);

    //run above function when document loads
    $(document).ready(function() {
      $("#flag").hide();
      getGeoJSON();
        //get points
        $.getJSON("./data/points_selected.geojson",{contentType: "application/json; charset=UTF-8"},function(data){
          var coords1 = data.features[session_key].geometry.coordinates[0][0];
          var coords2 = data.features[session_key].geometry.coordinates[0][1];
          map.panTo(new L.LatLng(coords2,coords1));
        // // map.panTo(new L.LatLng(coords2));
      });
      });


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

    var area = null; 

    map.on('draw:created', function (e) {
      var layer = e.layer;
      drawnItems.addLayer(layer);
      map.addLayer(drawnItems);
      area = L.GeometryUtil.geodesicArea(layer.getLatLngs());
    //dialog.dialog("open");
    console.log("run");
    console.log("this should be the area of polygon", area);
  });

  //goddammit this is still null.
  console.log("this should be the area of polygon2", area);
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
        //loads the new data- but does not seem to work?
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
      });
    };

    var postData = function(url,data){
      if ( !url || !data ) return;
      //data.cache = false;
      //data.timeStamp = new Date().getTime()
      $.post(url,
        data, function(d) {
      //console.log(d);
    });
    }


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
// var player = <?php //echo json_encode($_SESSION['username']); ?>;
// var player = 'hia';

//this function is called when button in 'submit' dialog is pressed.
//if this button is pressed, session variable 'land' needs to be updated.
function setData() {
  //purpose of vacant land
  var e = document.getElementById("usage");
  var usage = e.options[e.selectedIndex].text;
  console.log('user selected this', usage);

  //tracking the land count here. postdata is defined above:
  // postData( "index-username.php", {
  //   variable1: 1,
  //   variable2: 8
  // });

  //number of polygon that is searched. needs updating!
  var search_poly = 10;

  // ST_SetSRID(geometry geom, integer srid);
  drawnItems.eachLayer(function(layer){
    var sql = "INSERT INTO data_game (the_geom,no_falsified,search_polygon,created_at,usage, player1,tokensOfPlayer,area) VALUES (ST_SetSRID(ST_GeomFromGeoJSON('";

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
    //console.log(unixTime);
    var area = Math.round(L.GeometryUtil.geodesicArea(layer.getLatLngs()));

    var flagDefault = 0; 
    //OLD
    // var sql2 ='{"type":"Point","coordinates":[' + a.lng + "," + a.lat + "]}'),4326),'" + search_poly + "','" +  timeConvert(unixTime) + "','" + usage + "','" + enteredUsername + "','" + token + "','" + a.lat + "','" + a.lng +"')";
    postData( "index.php", {
      variable1: 1, //land
      // variable2: 8, let's for now assum you don't loose tokens
      variable3: area,
      enteredName: playername
    });

    console.log('playername',playername);

    //NEW
    var sql2 ='{"type":"MultiPolygon","coordinates":[[[' + coords + "]]]}'),4326),'" + flagDefault + "','" + search_poly + "','" +  timeConvert(unixTime) + "','" + usage + "','" + playername + "','" + token + "','" + area +"')";

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
}

//if post was sent new data is loaded
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
