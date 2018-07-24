<?php
session_start();

$pageWasRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0';
$NoSearchPolys =5;

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
	echo 'Session var before operation '.$_SESSION['token'];
	$_SESSION['token']=$_SESSION['token']-$_POST['tokenMinus'];
	echo 'Session var after operation '.$_SESSION['token'];
	echo 'bid '.$_POST['tokenMinus'];
} elseif (!isset($_SESSION['token'])) { //this should only happen when user just entered the game
    $_SESSION['token']=0;
    $_SESSION['count']=0;
    echo 'else statement, reason why token turns zero?'.$_SESSION['token']; 
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
<!-- //https://codepen.io/mfritsch/pen/VYdeEE -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Spot-a-lot</title>

  <!-- Bootstrap core CSS -->
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet"/>
 <!--  alternative css: glyphicon works but the rest gets messed up -->
 <!--  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"> -->

  <link rel="stylesheet" href="css/leaflet.css" />
  <link rel="stylesheet" href="css/leaflet.draw.css" />
  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css"/>
  <link rel="stylesheet" type="text/css" href="css/style.css"/>
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.1/css/all.css" integrity="sha384-O8whS3fhG2OnA5Kas0Y9l3cfpmYjapjI0E4theH4iuMD+pLhbf6JI0jIMfYcK3yZ" crossorigin="anonymous">

  <script src="js/leaflet.js"></script>
  <!--     <script src="js/leaflet.draw.js"></script> -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/0.4.2/leaflet.draw.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
  <!--  <script src='https://api.mapbox.com/mapbox.js/v3.1.1/mapbox.js'></script> -->
  <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
  <script src="js/jquery.redirect.js"></script>

  <!-- Bootstrap core JavaScript -->
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="https://d3js.org/d3.v4.min.js"></script>
  <script src="js/scores.js"></script>
   <!-- Bootstrap core JavaScript -->
<!--   <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> -->

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
  <!--   <div id="spinnerControls" style="display:none">
      <label id="spinnerLabel" for="spinner"></label>
      <input id="spinner" type="number">
      <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
    </div>
 -->
     <!-- -----------------the dialog element (using modal) for acquiring--------------------- -->
    <div class="modal fade" id="ModalBuy" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"><i class="fa fa-shopping-cart" aria-hidden="true"></i>  Acquire someone elses lot</h4>
        </div>
        <div class="modal-body" id="modalbody">
         <!--  spinner  -->
          <div id="spinnerControls">
            <label id="spinnerLabel" for="spinner"></label>
            <input id="spinner" type="number">
            <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-primary" data-dismiss="modal" aria-hidden="true">Cancel</button>
          <button id="acquireBtn" class="btn btn-primary">Acquire!</button>
        </div>
      </div>
      
    </div>
  </div>
</div>

    <!------------------------------- Navigation -------------------->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-bottom">
      <div class="container">
      	<a class="navbar-brand" onclick="addButtons()" value="addbuttons" style="cursor:pointer"><i class="fas fa-tree"></i> VACANT</a>
        <a class="navbar-brand" id="btnToken" style="cursor:pointer"><i class="fas fa-arrow-up"></i> SPOT ANOTHER</a>
        <a class="navbar-brand" href="scoreoverview.php"style="cursor:pointer; color:'#f05742'"><i class="fas fa-building"></i> BUILD</a>
       
    <!------------------------------- on layer click -------------------->
        <a id='flag' class="navbar-brand" style="display:none; cursor:pointer">FLAG</a>
        <a id='acquire' class="navbar-brand" style="display:none; cursor:pointer">ACQUIRE ME</a>

        <!--------------------- these are the control buttons for drawing (new)----------------------->
        <div id="controls" style="display:none">
          <button class="btn btn-primary" id="startBtn" aria-hidden="true">start mapping</button>
          <button class="btn btn-primary" id="deleteBtn" aria-hidden="true">delete</button>
          <button class="btn btn-primary" id="saveBtn" aria-hidden="true">claim</button>
        </div>
        <!--------------------- all the rest in navbar ----------------------->       
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
          <ul class="navbar-nav ml-auto">
            <li class="nav-item active">
              <a class="nav-link" href="logout.php">Logout
                <span class="sr-only">(current)</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">Rules</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <!--------------------- this is the container with land and tokens ----------------------->  
    <div class="container-fluid" style="pointer-events: none">
      <div class="row">
        <div class="col-md-12">
          <h1 class="mt-3"> <?php echo htmlspecialchars($_SESSION['username'])?>, SPOT A VACANT LOT!</h1>
        </div>
        <div class="col-md-2"></span>Your tokens: <?php echo $_SESSION['token']?> </div>
        <div class="col-md-2" id ="area"></div>
        <div class="col-md-2" id ="land"></div>
        <div class="col-md-4" id ="flags"></div>
        <div class="col-md-12" id="chart1"> <svg class="chart"></svg></div>
      </div>
    </div>
    <!-- Page Content -->
    <div id="map"></div>

  </div> <!--/.fluid-container-->

  <script>

//   function stuffToRezie(){
//         var h_window = $(window).height();
//         var h_map = h_window - 125;
//         $('#map').css('height', h_map);
// }

// $(window).on("resize", stuffToRezie).trigger('resize'); 
//----------------setting up all variables-----------
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
    var map = L.map('map',{ center: [51.51, -0.10], zoom: 22, zoomControl:false}); // Create Leaflet map object
    var cartoDBpoints = null;  //create a global variable, empty. stays empty at the moment?
    var cartoDBusername = "melanieimfeld";
    var playername = <?php echo json_encode($_SESSION['username']); ?>;  //get playername
    var playercolor = <?php echo json_encode($_SESSION['usercolor']); ?>;  //get playercolor
    var SQLquery = "SELECT * FROM data_game";  //SQL for data overview
    var flagged = false;   //boolean to check if flagging already occurred or not
    var token = <?php echo json_encode($_SESSION['token']); ?>;    //get tokens
    var session_key = <?php echo json_encode($_SESSION['array'][$_SESSION['count']]); ?>;
    var controlOnMap = false;  // Boolean global variable used to control visiblity
    var drawnItems = new L.FeatureGroup(); // Create variable for Leaflet.draw features
    var secured = true; // Create default boolean for secured
    var defBid = 5; // default minimum bid 
    var controlsVacant =false;
   
    // window.onresize = function() {
    //    var width = document.getElementById('map').clientWidth;
    //    console.log('flexwidth', width);
    // };
    
   
   // Add Tile Layer basemap
    L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}',{
      attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
      maxZoom: 18,
      subdomains:['mt0','mt1','mt2','mt3']
    }).addTo(map);

    poly = new L.Draw.Polygon(map, { //setting up the polygon shape
    allowIntersection: false,
    showArea: true,
    drawError: {
      color: '#15a956',
      timeout: 1000
    },
    shapeOptions: {
        stroke: true
      },
    guidelineDistance: 5,
    })


//------------------------ load scores---------------------
    //setInterval(getScores,5000, console.log('doesthiswork')); //update getScores ever 5 seconds. doesn't work?
     var cont1 = document.getElementById('area');
     var cont2 = document.getElementById('land');
     getScores(cont1,cont2);

 //  	var SQLquery2 = "SELECT current_owner,playercolor,sum(area) FROM data_game GROUP BY current_owner, playercolor";
	// //console.log('are these all the usernames', myjson);

	// function test(callback) {
	// 	  $.getJSON("https://"+cartoDBusername+".carto.com/api/v2/sql?format=JSON&q="+SQLquery2, function (data) {
	// 	    var container = document.getElementById('area');
	// 	    container.innerHTML=("Area total: "+data.rows[0].sum);
	// 	    //getDataCallback(data);
	// 	    console.log('goddamiit1', data.rows[0].sum);
	// 	  });
	// }

	// // function getDataCallback(data) {
	// //   console.log('goddamiit2', data);
	// // };

	// test();
	//console.log('goddamiit3', test());
	//console.log('hello obj', obj);

//------------------------ Functions---------------------
    //show controls when button 'vacant' is pressed
    function addButtons(){
      if (controlsVacant==false){
      console.log(document.getElementById('controls'));
      $("#controls").show(300);
      controlsVacant = true;
    } else {
      $("#controls").hide(300);
      controlsVacant = false;
    }
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

    jQuery(function($){
     //OnClick testButton do a POST to a login.php with user and pasword
      $("#btnToken").click(function(){
       $.redirect("map.php", {tokenBool: "true"}, "POST"); 
      });
     });

//------------------------ Function to load map ---------------------
    //get CARTO selection as geoJSON and add to leaflet map
    function getGeoJSON(){
      $.getJSON("https://"+cartoDBusername+".carto.com/api/v2/sql?format=GeoJSON&q="+SQLquery, function(data){

        var flags2 = 0;

        cartoDBpoints=L.geoJson(data, {
          style:hStyle,
          onEachFeature: function(feature, layer) {

          //count flags of current player
          if(layer.feature.properties.current_owner === playername){
            console.log('flags', layer.feature.properties.no_falsified);
            flags2 = flags2 + layer.feature.properties.no_falsified
          }

            //this happens on each feature when flag is clicked
            layer.on('click', function () {
              var modal=0;
              var entry =0;
              $("#flag").show(300);
              $("#acquire").show(300);
              $("#controls").hide(300);
              console.log('show cartodb id', feature.properties.cartodb_id);

              var globalX = feature.properties.cartodb_id;
              console.log('tis is this cartodbid', globalX);

              // ---------------add a flag to a property when flag is clicked
              $('#flag').click(function addFlag(){

                if (flagged==false){
                //here again. store a $.getjson command in variable. htf?
                //var globalX = 46;
                //var update2 = "UPDATE data_game SET no_falsified=no_falsified+1 WHERE cartodb_id="+globalX;

                var update = "UPDATE data_game SET no_falsified=no_falsified+1 WHERE cartodb_id="+globalX+"; DELETE FROM data_game WHERE no_falsified>2";

                submitToProxy(update);
                console.log('show update', update);
                alert("you just flagged a falsely classified property");
                flagged=true;
                console.log('id submitted', globalX);
                
                } else{
                alert("you can't flag this twice, sorry");
                };
              });

            // ---------------acquire------------
              //acquire property
              $('#acquire').click(function acquire(){
                modal = document.getElementById('modalbody');
                $("#ModalBuy").modal('show');
                //pop up dialog how much do you want to bid? you need to bid at least 1 token. the more you bid, the less likely someone will take over your property.
                var currentBid = feature.properties.bid_for + 1;
                var area = feature.properties.area;
                //var bid = document.getElementById("spinner").value;
                entry = document.createElement('p');
                var t = document.createTextNode("Your minimum bid is " + currentBid + " Tokens. Keep in mind that the more you bid the less likely someone will take it from you. If you purchase a lot that has been flagged 3 or more times, her/his penalty will be transferred");  
                entry.appendChild(t);
                modal.insertBefore(entry,modal.childNodes[0]);
                console.log('this is modalbody on acquire buttonclick', modal);
                //problem: if page gets refreshed you ALWAYS gain 1 token that means your purchase is + 1. you should only gain tokens when you refresh without action.
                
                //var spinner = e.options[e.selectedIndex].text;
                console.log('current value', currentBid);
 
 
			jQuery(function($){
                //conditions: larger than token size. larger than mimium amount. non negative number.
                $("#acquireBtn").click(function(e){
                  var bid = document.getElementById("spinner").value;
                  if (bid<=token && bid>=currentBid){
                  // var update = "UPDATE data_game SET bought_by="+ "'"+ playername +"'"+ ", bid_for="+ bid +"'"+", current_owner="+ playername +" WHERE cartodb_id="+globalX;

                  var update = "UPDATE data_game SET bought_by= '"+ playername +"' ,bid_for="+ bid +",current_owner= '"+ playername + "', playercolor='" + playercolor + "' WHERE cartodb_id="+globalX;
                  
                  console.log('this was submitted upon purchase', update);
                  submitToProxy(update);
                  console.log('this was submitted upon purchase', update);
                  console.log('tokencount', bid);

                  //update your area count and token count:
                  // postData( "index.php", {
                  //   variable2: bid, //subtract purchase from token count
                  //   variable3: area, //add area
                  //   enteredName: playername
                  // });

                  $.redirect("map.php", {tokenMinus: bid}, "POST"); 
                  alert('this lot is yours now');
                  $("#ModalBuy").modal('hide');

                  } else {
                  alert('you bid more than you have tokens or less than required');
                   //$("#ModalBuy").modal('hide');
                  }
                  //$("#modalbody").children().remove();

                });
              });//-----------end aquire--------

			});
                $('#ModalBuy').on('hidden.bs.modal', function () {
                  modal.removeChild(entry);
                  console.log('modal when modal is closed',modal);
                })
            });//---------layer.on

          
            layer.setStyle({
              color: feature.properties.playercolor
            });

            var label = L.marker(layer.getBounds().getCenter());
            layer.bindPopup('<b>Discovered by:</b> ' + feature.properties.player1 + '<br> <b>Area:</b> ' + feature.properties.area + '<br><b>current owner:</b> ' + feature.properties.current_owner + '<br><b>Minimum bid:</b> '+ (feature.properties.bid_for+1) + '<br><b>For sale:</b> '+feature.properties.secured+'<br>' +feature.properties.no_falsified+' <i class="fas fa-flag"></i>'+'');
          }//------on each feature
        }).addTo(map);
        //----------end L.geojson()
        
      // console.log('array',flags2);
      // $('#flags').text('Flags: '+ flags2);

      });
    }

//------------------------ load map on load---------------------
  $(document).ready(function() {
    $("#flag").hide();
    getGeoJSON();
        //get points
    $.getJSON("./data/points_selected.geojson",{contentType: "application/json; charset=UTF-8"},function(data){
        // add GeoJSON layer to the map once the file is loaded
        //console.log('pointsselected',data.features[session_key].geometry.coordinates[0][0]);
        //console.log('length',data.features.length);
        console.log('hello?',session_key);
        console.log('this is the id of search poly', data.features[session_key].properties.id);
        var coords1 = data.features[session_key].geometry.coordinates[0][0];
        var coords2 = data.features[session_key].geometry.coordinates[0][1];
        map.panTo(new L.LatLng(coords2,coords1));
    });
  });

  //this runs once drawn polygon is closed
  map.on('draw:created', function (e) {
    var layer = e.layer;
    map.addLayer(drawnItems);
    drawnItems.addLayer(layer);
    area = L.GeometryUtil.geodesicArea(layer.getLatLngs());
    //dialog.dialog("open");
    console.log("run");
    console.log("this should be the area of polygon", area);
  });


//------------------------ submit to proxy---------------------
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

    // var submitToProxy2 = function(q){
    //   $.post("php/callProxy.php", { // <--- Enter the path to your callProxy.php file here
    //     qurl:q,
    //     cache: false,
    //     timeStamp: new Date().getTime()
    //   }, function(data) {
    //     console.log(data);
    //   });
    // };

    var postData = function(url,data){
      if ( !url || !data ) return;
      //data.cache = false;
      //data.timeStamp = new Date().getTime()
      console.log("Loaded");
      $.post(url,
        data, function(d) {
        console.log(d);
        console.log("posted");
      });
    }


function getRandomArbitrary(min, max) {
  return Math.floor(Math.random() * (max - min) + min);
}

//console.log('sessionkey', session_key);
//console.log('sessionarray', session_array);

    var drawControl = new L.Control.Draw({
      position: 'topright',
      draw:false,
      edit:false     
    });


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


    // Your script will go here!
    // Function to run when feature is drawn on map

    var area = null; 

  //goddammit this is still null.
  console.log("this should be the area of polygon2", area);
// ----------------the dialog to collect information----------------------
//   Use the jQuery UI dialog to create a dialog and set options
dialog = $("#dialog").dialog({
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
  setData();
});


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

  // ST_SetSRID(geometry geom, integer srid);
  drawnItems.eachLayer(function(layer){
    var sql = "INSERT INTO data_game (the_geom,no_falsified,search_polygon,created_at,usage, player1,tokensOfPlayer,area,bid_for,current_owner,playercolor,secured) VALUES (ST_SetSRID(ST_GeomFromGeoJSON('";
    
    //var search_poly =  layer.features[session_key].properties.cartodb_id;
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

    console.log('playername',playername);
    //console.log('search poly',search_poly);

    //NEW. This is very unelegant but gets data from json1 and submits new data to cartodb
    $.getJSON("./data/points_selected.geojson",{contentType: "application/json; charset=UTF-8"},function(data){
        console.log('this is the id of search poly', data.features[session_key].properties.id);
 
        var sql2 ='{"type":"MultiPolygon","coordinates":[[[' + coords + "]]]}'),4326),'" + flagDefault + "','" + data.features[session_key].properties.id + "','" +  timeConvert(unixTime) + "','" + usage + "','" + playername + "','" + token + "','" + area + "','" + defBid + "','" + playername + "','" + playercolor + "','" +secured+"')";

        var pURL = sql+sql2;
        console.log(pURL)
        //this is the function that will submit the request to proxy. see line 170
        submitToProxy(pURL);
        console.log("Feature has been submitted to the Proxy");
  
    });
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
    map.removeLayer(cartoDBpoints);
    console.log('map had cartodb points');
  };
  getGeoJSON();
  console.log('layer refreshed');
};

</script>

</body>

</html>
