<?php
  include "functions.php";
?>
<!DOCTYPE html>
<html lang="DE">
<head>
  <meta charset="UTF-8">
  <title>Walkydoo - Routen</title>
  <link rel="stylesheet" href="style.css" />

  <!-- leaflet -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
          integrity="sha384-RFZC58YeKApoNsIbBxf4z6JJXmh+geBSgkCQXFyh+4tiFSJmJBt+2FbjxW7Ar16M"
          crossorigin="anonymous"></script>

  <!-- marker -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.3.0/dist/MarkerCluster.css" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.3.0/dist/MarkerCluster.Default.css" />
  <script src="https://unpkg.com/leaflet.markercluster@1.3.0/dist/leaflet.markercluster.js"
          integrity="sha384-1artbd0pdGdZ72+IcKWkY1So1xu4Hzygfd0cVLSs7f5lBZZ/FhyEZc4UyQR3DT9c"
          crossorigin="anonymous"></script>

  <!-- geocoder -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
  <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"
          integrity="sha384-zLuUHJ9GuAH1bHA3YCTQl5y4SwBQ387TcN+d7zfcR3F0tKtEmvQ6veTeaPfPjdfK"
          crossorigin="anonymous"></script>

  <!-- geoJSON files -->
  <script src= "geoJSON/dogParks.geojson" type="text/javascript"></script>
  <script src= "geoJSON/excrementBags.geojson" type="text/javascript"></script>

  <style>
    #mapid {
      height: 700px;
    }
  </style>
</head>
<body>
  <nav>
    <img class="logo" src="img/logo.png" alt="walkydoo logo">
    <ul>
        <li><a href="index.php">HOME</a></li>
        <?php if (isset($_SESSION['user'])) { ?>
        <li><a href="routen_edit.php">ROUTEN</a></li>
        <li><a href="profil.php">PROFIL</a></li>
        <li><a href="logout.php">LOGOUT</a></li>
        <?php } 
        else { ?>
        <li><a href="routen.php">ROUTEN</a></li>
        <li><a href="login.php">LOGIN</a></li>
        <?php } ?>
    </ul>
  </nav>
  <main>
  <div class="container">
    <div id="mapid"></div>
    <div class="buttons1">
      <button onclick=EditMap() id="button1" class= "button1" name="draw"></button>
      <button onclick=Save() id="button2" class= "button2" name="save">Speichern</button>
      <button onclick=DeleteAll() id="button2" class= "button2" name="delete">Abbrechen</button>
      <button onclick=DeleteLast() id="button2" class= "button2" name="deleteLast">Punkt löschen</button>
    </div>

<script>

    /* Karte einbinden */
    var map = new L.Map('mapid');

    /* Location search */
    var search = new L.Control.geocoder( { position: 'topleft' } ).addTo(map);

    /* Layers */
    var light = new L.TileLayer('https://{s}.tile.openstreetmap.de/tiles/osmde/{z}/{x}/{y}.png', {  
      attribution: ['Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors', 'POI via <a href="http://www.overpass-api.de/">Overpass API</a>']
    });
    light.addTo(map);

    /* aktueller User-Standort */
    function onLocationFound(e) {
      L.circle(e.latlng, e.accuracy/4).addTo(map).bindPopup('Mein Standort').openPopup();
    }

    function onLocationError(e) {
      map.setView(new L.LatLng(47.96692, 13.1047), 8);
      alert("Ihr aktueller Standort wird nicht abgefragt! Sie können manuell über die Suchfunktion einen Standort abfragen.");
    }

    map.on("locationfound", onLocationFound);
    map.on("locationerror", onLocationError);

    map.locate({ setView: true, maxZoom: 16 });

    /* Marker styles */
    var excrementBags_Icon = L.icon({
          iconUrl: 'img/sackerl.png',
          iconSize:[35,45]
        });
    var dogParks_Icon = L.icon({
          iconUrl: 'img/wiese.png',
          iconSize:[35,45]
        });

    /* MarkerCluster */
    var markers = L.markerClusterGroup( { showCoverageOnHover: true, zoomToBoundsOnClick: true } );

    /* Marker für excrementBags */
    var excrementBags_Marker = new L.GeoJSON(excrementBags, {
      pointToLayer: (feature, latlng) => {
        ex_Marker = L.marker(latlng, { icon: excrementBags_Icon });
        markers.addLayer(ex_Marker.bindPopup('Hundetoilette').openPopup());
      }
    });

    /* Marker für dogParks */
    new L.GeoJSON(dogParks, {
      pointToLayer: (feature, latlng) => {
        dP_Marker = L.marker(latlng, { icon: dogParks_Icon });
        markers.addLayer(dP_Marker.bindPopup('Hundewiese').openPopup());
      },
      //Wenn geoJSON Polygonfläche gespeichert hat, dann durch Marker ersetzen
      onEachFeature: (feature = {}, layer) => {
        const { properties = {} } = feature;
        const { Name } = properties;
        
      if (feature.geometry.type == 'Polygon') {
          layer.setStyle({
              'weight': 0,
              'fillOpacity': 0
          });
          var bounds = layer.getBounds();
          var center = bounds.getCenter();
          dP_Marker = L.marker(center, {icon: dogParks_Icon });

          markers.addLayer(dP_Marker.bindPopup('Hundewiese').openPopup());
        }
      }
    });

    /* MarkerCluster */
    map.addLayer(markers);

    /* Eigene Ableitung der Klasse Polyline */
    PolylineId = L.Polyline.extend({
      options: { 
        routeid: '0'
      }
    });

    var existing_routes = new Array();
    fetch("routen_sql.php?")
    .then(function(response) {
      response.text().then(function (text) {
        var response_array = text.split('%');
        var route_id = 0;
        var tmp_route = new Array();
        var walking_distance = 0;
        console.log(response_array);
        for(let element of response_array){
          console.log(element);
          var items = element.split('$');
          
          if(route_id == 0)
          {
            route_id = items[0];
            tmp_route.push([items[2], items[3]]);
            walking_distance = items[1];
          }
          else if(items[0] == route_id)
          {
            // point belongs to route => add to array
            tmp_route.push([items[2], items[3]]);
            walking_distance = items[1];
          }
          else if(items[0] != route_id)
          {
            // new - route -> draw prev. one and add new point to array
            console.log(tmp_route);
            var firstpolyline = new PolylineId(tmp_route, {
              color: '#F4B213',
              weight: 5,
              opacity: 1,
              smoothFactor: 1,
              routeid: route_id
            });
            
            let minutes = walking_distance* 60 /4000;
            let hours = 0;
            let walking_time = minutes.toFixed(0) + " Minuten";
            
            if (minutes >= 60)
            {
                hours = minutes / 60;
                minutes -= (hours*60);

                walking_time = hours.toFixed(0) + " Stunden" + minutes.toFixed(0) + " Minuten";
            }
            
            firstpolyline.bindPopup("Gehzeit: " + walking_time).openPopup();
            firstpolyline.on("click", function (e) {
              fetch("set_routen_id.php?routen_id="+this.options.routeid)
              .then(function(response) {
                response.text().then(function (text) {
                  console.log(text);
                });
              });
            });
            firstpolyline.addTo(map);
            
            // add to an array to reference it later if needed
            existing_routes.push(firstpolyline);
            
            route_id = items[0];
            tmp_route = [];
            tmp_route.push([items[2], items[3]]);
          }
          else
          {
            // route zeichnen und id auf 0 setzen 
            route_id = 0;
            tmp_route = [];
          }
        }
      });  
    });
    
    /* Karte editieren */
    var coordinates = new Array();
    var distance = 0; 
    var draw_flag = false; //prüft, ob gezeichnet wird oder nicht
    var tmppolyline = null;
    var tmpcircle = null;

    // event-hanlder, added punkt ins tmp_array wenn die map geklickt wird und wenn zeichnen aktiv ist
    map.on('click', function (e) {
      if(draw_flag)
      {
        let point = new L.latLng(e.latlng.lat, e.latlng.lng);
        coordinates.push(point);
        // Ersten Klick als Punkt zeichnen
        if(coordinates.length == 1)
        {
          tmpcircle = new L.Circle(coordinates[0], 5, {
            color: '#F4B213'
          });
          tmpcircle.addTo(map);
        }
        // linie zeichnen wenn mehr als 1 Koordinate eingetragen sind
        // alte Linie muss zuvor gelöscht werden
        else if(coordinates.length > 1)
        {
          if(tmpcircle != null)
          {
            tmpcircle.remove(map);
            tmpcircle = null;
          }
          
          distance += point.distanceTo(coordinates[coordinates.length-2]);
          if(tmppolyline != null)
          {
            tmppolyline.remove(map);

          }
            

          tmppolyline = new L.Polyline(coordinates, {
            color: '#F4B213',
            weight: 5,
            opacity: 1,
            smoothFactor: 1
          });
          tmppolyline.addTo(map);
          walking_time = calcWalkingTime(distance);
          tmppolyline.bindPopup("Gehzeit: " + walking_time).openPopup();
        }
      }
    });
    
    function EditMap() {
      // Buttons show/hide
      var x = document.getElementsByClassName("button2");
      for(let element of x){
        element.style.display = "block";
      };
      document.getElementsByClassName("button1")[0].style.display = "none";
      
      coordinates = [];
      distance = 0;
      draw_flag = true;
    }
      
    /* gezeichnete Route speichern */
    function Save() {

      draw_flag = true; //zeichnen beginnen
      var meters = distance.toFixed(0);
      var index = 0;

      fetch("meter_sql.php?q="+meters)
      .then(response => response.json())
      .then(routen_id => {
        
        for(let element of coordinates){
          var lat = element.lat;
          var lng = element.lng;
          lat = lat; 
          lng = lng; 

          fetch("coordinates_sql.php?lat="+lat.toString()+"&lng="+lng.toString()+"&routen_id="+routen_id+"&index="+index)
          .then(function(response) {
            response.text().then(function (text) {
              console.log(text);
            });
          });
          index++;
        }
        });
      // Buttons show/hide
      var button = document.getElementsByClassName("button2");
      for(let element of button){
        element.style.display = "none";
      };
      document.getElementsByClassName("button1")[0].style.display = "block";

      draw_flag = false; //zeichnen beenden
    }

    /* letzten Punkt löschen */
    function DeleteLast() {
      if (draw_flag && coordinates.length > 1)
      {
        distance -= coordinates[coordinates.length-1].distanceTo(coordinates[coordinates.length-2]);
        coordinates.pop();
        if (tmppolyline != null)
            tmppolyline.remove(map);
        
        tmppolyline = new L.Polyline(coordinates, {
          color: '#F4B213',
          weight: 5,
          opacity: 1,
          smoothFactor: 1
        });

        tmppolyline.addTo(map);
      }
    }

    /* ganze Polyline löschen */
    function DeleteAll() {
      if (draw_flag && coordinates.length > 0)
      {
        coordinates = [];
        distance = 0;
        
        if (tmppolyline != null)
        {
          tmppolyline.remove(map);
          tmppolyline = null;
        }

        draw_flag = false;
      }
      // Buttons show/hide
      var button = document.getElementsByClassName("button2");
      for(let element of button){
        element.style.display = "none";
      };
      document.getElementsByClassName("button1")[0].style.display = "block";
    }

    function calcWalkingTime(distance)
    {
      let minutes = distance* 60 /4000;
      let hours = 0;
      let walking_time = minutes.toFixed(0) + " Minuten";
            
      if (minutes >= 60)
      {
          hours = Math.floor(minutes / 60);
          minutes = minutes % 60;

          walking_time = hours.toFixed(0) + " Stunde(n) " + minutes.toFixed(0) + " Minute(n) ";
      }

      return walking_time;
    }
</script>

<?php
    include "footer.php";
?>


