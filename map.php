<?php
include 'php/cartoDBProxy.php';
session_start();

$pageWasRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0';
$NoSearchPolys =112;

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

// $name =  $_SESSION["username"];
// $queryToken = "SELECT * FROM data_game WHERE player1='$name' order by created_at desc LIMIT 1";
// $queryBool = "UPDATE data_game SET subtracted_tokens=false WHERE player1='$name'";
// $acquired = goProxy($queryToken);
// $acquired =  json_decode($acquired, true);

// if ($acquired["rows"][0]["subtracted_tokens"]== 1){ //if result is true aka result was not checked
// 	echo 'hello';
// 	$won = $acquired["rows"][0]["bid_for"];
// 	$_SESSION['token']= $_SESSION['token']+ $won;
//   //echo $_SESSION['token'];
//   goProxy($queryBool);
// } else {
//   echo 'nope';
// }
//var_dump($acquired);

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
          <p id="acquireText"></p>
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
      	<a class="navbar-brand" onclick="addButtons()" value="addbuttons" style="cursor:pointer"><i class="fas fa-pen"></i> MAP VACANT</a>
        <a class="navbar-brand" id="btnToken" style="cursor:pointer"><i class="fas fa-search"></i> SPOT ANOTHER</a>
        <a class="navbar-brand" href="scoreoverview.php"style="cursor:pointer; color:'#f05742'"><i class="fas fa-building"></i> BUILD</a>
        <a class="navbar-brand" id="btnUp" style="cursor:pointer; color:'#f05742'"><i class="fas fa-arrow-up"></i> SCORES</a>
       
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
              <a class="nav-link" href="rules.php">Rules
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
    <div class="container-fluid mx-3" id="scores1" style="pointer-events: none">
      <div class="row">
        <div class="col-md-12">
         <div class="mt-3 wrapper">
            <h1> Current scores for <?php echo htmlspecialchars($_SESSION['username'])?></h1>
          </div>
        </div>
        <div class="col-md-12"> <hr></div>
        <div class="col-md-2"></span>Your tokens: <?php echo $_SESSION['token']?> </div>
        <div class="col-md-2" id ="area"></div>
        <div class="col-md-2" id ="land"></div>
        <div class="col-md-4" id ="flags"></div>
        <div class="col-md-12" id="chart1"> <svg class="chart"></svg></div>
    </div>
  </div>

  </div> <!--/.fluid-container-->
  <div id="map"></div>
<!-- 
 <div class="container-fluid">
    <div id="map"></div>
</div> -->


  <div class="modal fade" id="hello" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <div class="modal-title">
              <h2 class="typeit"><?php echo htmlspecialchars($_SESSION['username'])?>, SPOT A VACANT LOT!</h2>
          </div>
        </div>
        <div class="modal-body" id="modalbody">
            <p>The goal of this game is to find as much vacant land as possible and secure it. You can increase your area in two ways: <span style="background-color: #cfead9">Click through the images and map vacant land or take lots away from others.</span>To do so, simply click on a lot that you want to acquire. Read more about the rules <span style="background-color: #cfead9"><a href="rules.php"> here </a></span>. Have fun exploring!</p>
            </div>
        <!-- end of dropdown -->
        </div>
      </div>
      
    </div>
  </div>


<script>

// $(window).on("resize", stuffToRezie).trigger('resize'); 
//----------------setting up all variables-----------
  //counting site visits
  var counter = 0; //i think this is unnecessary?
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
  var cartoDBusername = "melanieindemfeld";
  var playername = <?php echo json_encode($_SESSION['username']); ?>;  //get playername
  var playercolor = <?php echo json_encode($_SESSION['usercolor']); ?>;  //get playercolor
  var SQLquery = "SELECT * FROM data_game";  //SQL for data overview
  var flagged = false;   //boolean to check if flagging already occurred or not
  var token = <?php echo json_encode($_SESSION['token']); ?>;    //get tokens
  var session_key = <?php echo json_encode($_SESSION['array'][$_SESSION['count']]); ?>;
  var controlOnMap = false;  // Boolean global variable used to control visiblity
  var drawnItems = new L.FeatureGroup(); // Create variable for Leaflet.draw features
  var secured = true; // Create default boolean for secured
  var defBid = 1; // default minimum bid 
  var controlsVacant =false;
  var myScore = {area:0, land:0}; //empty array to hold displayed scores
  var flagDefault = 0; 
  var usage = "default"; 
  var defToken = false; //token default for acquiration-check
  var cont1 = document.getElementById('area'); //fill html element for scores
  var cont2 = document.getElementById('land');
  var priorLoc =document.referrer; //prior loc of user
  var icon = L.icon({
  iconUrl: 'css/star.png',
  iconSize:     [20, 20], // size of the icon
  iconAnchor:   [10, 20], // point of the icon which will correspond to marker's location
  popupAnchor:  [10, 20] // point from which the popup should open relative to the iconAnchor
  });

 
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

  //disable controls
  // zoomControl:false (goes in the above paort)
  map.dragging.disable();
  map.touchZoom.disable();
  map.doubleClickZoom.disable();
  map.scrollWheelZoom.disable();
  map.boxZoom.disable();

//------------------------ load scores---------------------
  //setInterval(getScores,5000, console.log('doesthiswork')); //update getScores ever 5 seconds. doesn't work?
  
  getScores(cont1,cont2);
  //console.log('priorloc', priorLoc);

//------------------------show modal depending on where user came from---------------------
  if(priorLoc.indexOf('map.php') == -1 && priorLoc.indexOf('rules.php') == -1 && priorLoc.indexOf('scoreoverview.php') == -1 && priorLoc) {
      $('#hello').modal('show');
  } //if priorloc is not one of the above and existent, show modal

  document.getElementById("btnUp").onclick = function(){
    scroll("scores1");
  }

//------------------------ Functions---------------------
  //show controls when button 'vacant' is pressed
  function addButtons(){
    if (controlsVacant==false){
    console.log(document.getElementById('controls'));
    $("#controls").show(300);
    $("#flag").hide(300);
    $("#acquire").hide(300);
    controlsVacant = true;
  } else {
    $("#controls").hide(300);
    controlsVacant = false;
  }
    console.log('status of controls', controlsVacant);
    //$.post("index-username.php", {"update": 10});
    //document.getElementById('controls').style.display = "block";
  }

       //if plot is classified as bought make opacity zero 
  function getOpacity(d) {
      return d == false ? 1 :
            d == true  ? 0.3:
              0.5;
  };

  jQuery(function($){
   //OnClick testButton do a POST to a login.php with user and pasword
    $("#btnToken").click(function(){
       $.redirect("map.php", {tokenBool: "true"}, "POST");
    });
   });

//------------------------ function on each layer---------------------

  function scroll(id) {
  var elmnt = document.getElementById(id);
  elmnt.scrollIntoView();
  }

//------------------------ Function to load map ---------------------
  //get CARTO selection as geoJSON and add to leaflet map
  function getGeoJSON(){
    $.getJSON("https://"+cartoDBusername+".carto.com/api/v2/sql?format=GeoJSON&q="+SQLquery, function(data){
     
    cartoDBpoints=L.geoJson(data, {
      style:hStyle,
      onEachFeature: onEachFeature
    }).addTo(map);

    });
  };

//------------------------ function on each layer---------------------
      function onEachFeature(feature, layer) {
          var flagged=false;
          //bind click
            layer.on({
                click: whenClicked
            });

          layer.setStyle({
            color: feature.properties.playercolor,
            fillOpacity: getOpacity(feature.properties.secured)
          });

          var label = L.marker(layer.getBounds().getCenter());
          layer.bindPopup('<b>Discovered by:</b> ' + feature.properties.player1 + '<br> <b>Area:</b> ' + feature.properties.area + '<br><b>Current owner:</b> ' + feature.properties.current_owner + '<br><b>Minimum bid:</b> '+ (feature.properties.bid_for+1) + '<br><b>For sale:</b> '+feature.properties.secured+'<br>' +feature.properties.no_falsified+' <i class="fas fa-flag"></i>'+'');
      }

//------------------------ function when clicked---------------------
  function whenClicked(e) {
        var layerID = e.target.feature.properties.cartodb_id;
        var sec = e.target.feature.properties.secured;
        console.log('cartobd id of clicked',e.target.feature.properties.cartodb_id);

         if(sec) { //only show controls if lot is 'acquirable'
                  $("#flag").show(300);
                  $("#acquire").show(300);
                  $("#controls").hide(300);
                  controlsVacant = false;
              } else {
                  $("#acquire").hide(300);
                  $("#flag").hide(300);
             }

  //------------------------ flagging---------------------
         $('#flag').click(function addFlag(event){
          console.log("this is inside flag button");
              
              if (flagged!=true){

              var update = "UPDATE data_game SET no_falsified=no_falsified+1 WHERE cartodb_id="+layerID+"; DELETE FROM data_game WHERE no_falsified>2";

              submitToProxy(update);
              console.log('show update', update);
              alert("you just flagged a falsely classified property");
              flagged=true;
              console.log('id submitted', layerID);
              } 

          });
      if (flagged==true){
        alert("you can only flag once per session, sorry");
      }
  //------------------------ aquiring---------------------
           $('#acquire').off('click').click(function acquire(ev){ //???????
              modal = document.getElementById('modalbody'); //the modal to append elements to
              var currentBid = e.target.feature.properties.bid_for + 1;
              var bid = document.getElementById("spinner").value;
       

              $("#ModalBuy").modal('show');

              console.log('this is modalbody when acquire is clicked', modal);
 
              //pop up dialog how much do you want to bid? you need to bid at least 1 token. the more you bid, the less likely someone will take over your property.
  
              //entry = document.createElement('p');
              //entry.className = "par";
              $('#acquireText').text("Your minimum bid is " + currentBid + " Tokens. The higher the amount of tokens you bid, the more protected your lot is from another bid. Fill in your bid here:");
              //entry.appendChild(t);
              //modal.insertBefore(entry,modal.childNodes[0]);
              //problem: if page gets refreshed you ALWAYS gain 1 token that means your purchase is + 1. you should only gain tokens when you refresh without action.
              
              //var spinner = e.options[e.selectedIndex].text;
              console.log('current value', currentBid);

             jQuery(function($){
              //conditions: larger than token size. larger than mimium amount. non negative number.
              $("#acquireBtn").off('click').click(function(e){
                console.log('this is modalbody when acquire button is clicked', modal);
                //e.stopPropagation();
                var bid = document.getElementById("spinner").value;
                if (bid<=token && bid>=currentBid){
                // var update = "UPDATE data_game SET bought_by="+ "'"+ playername +"'"+ ", bid_for="+ bid +"'"+", current_owner="+ playername +" WHERE cartodb_id="+globalX;

                var update = "UPDATE data_game SET bought_by= '"+ playername +"' ,bid_for="+ bid +",current_owner= '"+ playername + "', playercolor='" + playercolor + "' WHERE cartodb_id="+layerID;
                
                submitToProxy(update);
                console.log('this was submitted upon purchase', update);
                console.log('tokencount', bid);

                $.redirect("map.php", {tokenMinus: bid}, "POST"); 
                alert('this lot is yours now');
                $("#ModalBuy").modal('hide');

                } else {
                alert('you bid more than you have tokens or less than required');
                $("#ModalBuy").modal('hide');
                }
         
              });
            });
                      $('#ModalBuy').on('hidden.bs.modal', function () {
                        //modal.removeChild(entry);
                        //$('.par').remove();
                        console.log('modal when modal is closed',modal);                
                        $("#flag").hide(300);
                        $("#acquire").hide(300);
                      })

    });//-----------end aquire--------
           
  } //end of function when clicked

//------------------------ other stuff---------------------
function getSearchArea(){
    $.getJSON("./data/100_features_GiGL.geojson",{contentType: "application/json; charset=UTF-8"},function(data){
      // add GeoJSON layer to the map once the file is loaded
      console.log('this is session key',session_key);
      console.log('this is the id of search poly', data.features[session_key].properties.SiteID);
      //console.log('pointsselected',data.features[session_key].geometry.coordinates[0]);
      //console.log('length',data.features.length);
      var coords1 = data.features[session_key].geometry.coordinates[0];
      var coords2 = data.features[session_key].geometry.coordinates[1];
      map.panTo(new L.LatLng(coords2,coords1));
      var label2 = new L.marker([coords2,coords1],{icon: icon}).addTo(map);

  });
}

//------------------------ load map on load---------------------
$(document).ready(function() {
  scroll("map");
  $("#flag").hide();
  getGeoJSON(); //get polygons
  getSearchArea(); //get centroids
      
});

//------------------------features that run when polygon is closed---------------------
map.on('draw:created', function (e) {
  var layer = e.layer;
  map.addLayer(drawnItems);
  drawnItems.addLayer(layer);
  //dialog.dialog("open");
  console.log("run");
  console.log("show me the drawn items", drawnItems);
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
    setData(); //submits all polygon data
  }
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

// var player = 'hia';

//this function is called when button in 'submit' dialog is pressed.
//if this button is pressed, session variable 'land' needs to be updated.
function setData() {

// ST_SetSRID(geometry geom, integer srid);
drawnItems.eachLayer(function(layer){
  var sql = "INSERT INTO data_game (the_geom,no_falsified,search_polygon,created_at,usage, player1,tokensOfPlayer,area,bid_for,current_owner,playercolor,secured,subtracted_tokens) VALUES (ST_SetSRID(ST_GeomFromGeoJSON('";
  
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
  console.log('playername',playername);
  //console.log('search poly',search_poly);

  //NEW. This is very unelegant but gets data from json1 and submits new data to cartodb
  $.getJSON("./data/100_features_GiGL.geojson",{contentType: "application/json; charset=UTF-8"},function(data){
      //console.log('this is the id of search poly', data.features[session_key].properties.id);

      var sql2 ='{"type":"MultiPolygon","coordinates":[[[' + coords + "]]]}'),4326),'" + flagDefault + "','" + data.features[session_key].properties.SiteID + "','" +  timeConvert(unixTime) + "','" + usage + "','" + playername + "','" + token + "','" + area + "','" + defBid + "','" + playername + "','" + playercolor + "','"+secured+"','"+defToken+"')";

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
//dialog.dialog("close");
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
<script src="js/typeit.js"></script>
</body>

</html>
