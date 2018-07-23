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
            <p>You can secure your lot from being bought by others by building something on it. Only the options that fulfil the area requirements are shown. Keep in mind your tokencount.</p>
        <!-- tryin to put a dropdown in the modal. on submit, send the content to xx --> 
        <!-- <form name="searchForm" id="searchForm" method="POST" action="php/build.php"> -->
            <input type="hidden" name="selection" id="buildInput"> 
            <div class="dropdown">
              <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Building options
              <span class="caret"></span></button>
              <input id="search_type" name="search_type" type="hidden">
              <ul class="dropdown-menu" id="dropdownBuild">
              </ul>
            </div>
        <!-- end of dropdown -->
        </div>
        <div class="modal-footer">
          <button class="btn btn-primary" data-dismiss="modal" aria-hidden="true">Cancel</button>
          <button id="allSubmitBtn" class="btn btn-primary">Build!</button>
        </div>
      </div>
      
    </div>
  </div>
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
        <a class="navbar-brand" href="map.php"><button type="button" href="map.php" class="btn btn-primary">SPOT MORE</button></a>
        <a class="navbar-brand" style="cursor:pointer"><i class="fas fa-arrow-up"></i> BUILD</a>
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
              <a class="nav-link" href="#">Rules</a>
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

    <div class="row">
        <div class="col-md-5" id="chart1">
          <h3 class="mt-3">Building options</h3>
          <table class="table table-hover" style="width:100%">
            <tr>
              <th>Type</th>
              <th>Max. Area (sqm)</th> 
              <th>Cost (Tokens)</th>
            </tr>
            <tr>
              <td>Hospital</td>
              <td>4'000</td> 
              <td>70</td>
            </tr>
            <tr>
              <td>Shopping Temple</td>
              <td>2'000</td> 
              <td>57</td>
            </tr>
            <tr>
              <td>School</td>
              <td>2'000</td> 
              <td>20</td>
            </tr>
             <tr>
              <td>Housing</td>
              <td>700</td> 
              <td>7</td>
            </tr>
            <tr>
              <td>Community Center</td>
              <td>200</td> 
              <td>2</td>
            </tr>
             <tr>
              <td>Grocery Store</td>
              <td>100</td> 
              <td>1</td>
            </tr>
              <tr>
              <td>Community garden</td>
              <td>100</td> 
              <td>1</td>
            </tr>
            <tr>
              <td>Pop up bar</td>
              <td>50</td> 
              <td>1</td>
            </tr>
          </table>
          <svg class="chart"></svg>
        </div>
        <div class="col-md-7">
          <div id="map" class="mt-3"></div>
        </div>
      </div>
    </div>
  </div>

    <!-- Page Content -->
    

  </div> <!--/.fluid-container-->

<script>

function stuffToResize(){
        var h_window = $(window).height();
        var h_map = h_window - 70;
        $('#map').css('height', h_map);
}

function build(){
  var y = $('#search_type').val('store');
  console.log('what is this', y); 
  //var z = $('#searchForm').submit();
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

    //write the SQL query I want to use
    var SQLquery = "SELECT * FROM data_game";

    var playername = <?php echo json_encode($_SESSION['username']); ?>;  //get playername

     // Create Leaflet map object
    var map = L.map('map',{ center: [51.5310, 0.1007], zoom: 15, zoomControl:true});

    map.scrollWheelZoom.enable();

    //update getScores ever 5 seconds
    setInterval(getScores(),5000);

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
        return d == true ? 1 :
              d == false  ? 0.1:
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
              $(".dropdown-menu li").click(function(){
                selText = $(this).text();
                console.log('value selected', selText);
                //$(this).parents('.btn-group').find('.dropdown-toggle').html(selText+'<span class="caret"></span>');
                $("#allSubmitBtn").click(function(e){
                  var sql ="UPDATE data_game SET usage='"+selText+"', secured="+false+" WHERE cartodb_id="+globalX;
                  console.log(sql)
                  //this is the function that will submit the request to proxy. see line 170
                  submitToProxy(sql);
                  $("#myModal").modal('hide');
                  $("#dropdownBuild").children().remove();
                  console.log('this is the list', list);
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
