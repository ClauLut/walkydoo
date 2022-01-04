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

    // var dark = L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_smooth_dark/{z}/{x}/{y}{r}.png', {
    //   maxZoom: 20,
    //   attribution: '&copy; <a href="https://stadiamaps.com/">Stadia Maps</a>, &copy; <a href="https://openmaptiles.org/">OpenMapTiles</a> &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors'
    // });
    // dark.addTo(map);

    /* Layer controller*/
    // var baseMaps = {
    //   "Light": light,
    //   "Dark": dark
    // };
    // var controlLayers = L.control.layers(baseMaps, null, { collapsed: false });
    // controlLayers.addTo(map);

    
    /* aktueller User-Standort */
    function onLocationFound(e) {
      L.circle(e.latlng, e.accuracy).addTo(map).bindPopup('Mein Standort').openPopup();
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

    /* Laden der vorhandenen Routen bei Seitenaufruf */
    var existing_routes = new Array();
    fetch("routen_sql.php?")
    .then(function(response) {
      response.text().then(function (text) {
        var response_array = text.split('%');
        var route_id = 0;
        var tmp_route = new Array();
        console.log(response_array);
        for(let element of response_array){
          console.log(element);
          var items = element.split('$');
          
          if(route_id == 0)
          {
            route_id = items[0];
            tmp_route.push([items[2], items[3]]);
          }
          else if(items[0] == route_id)
          {
            // point belongs to route => add to array
            tmp_route.push([items[2], items[3]]);
          }
          else if(items[0] != route_id)
          {
            // new - route -> draw prev. one and add new point to array
            console.log(tmp_route);
            var firstpolyline = new L.Polyline(tmp_route, {
              color: '#F4B213',
              weight: 5,
              opacity: 1,
              smoothFactor: 1
            });
            let walking_time = items[1]*60/3600;
            firstpolyline.bindPopup("Gehzeit: " + walking_time.toFixed(0) + " Minuten").openPopup();
            firstpolyline.addTo(map);
            
            // add to an array to reference it later if needed
            existing_routes.push(firstpolyline);
            
            route_id = items[0];
            tmp_route = [];
            tmp_route.push([items[2], items[3]]);
          }
          else
          {
            var firstpolyline = new L.Polyline(tmp_route, {
              color: '#F4B213',
              weight: 5,
              opacity: 1,
              smoothFactor: 1
            });
            firstpolyline.addTo(map);
            // route zeichnen, id auf 0 zurücksetzten
            route_id = 0;
            tmp_route = [];
          }
        }
      });  
    });
</script>

<?php
    include "footer.php";
?>


