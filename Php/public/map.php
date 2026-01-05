<?php
require_once __DIR__ . "/../config/bootstrap.php";
$lat = $_SESSION['coords']['lat'];
$lng = $_SESSION['coords']['lon'];
$geojson = [
    'type' => 'FeatureCollection',
    'features' => []
];

require_once __DIR__ . '/../action/GetCirculationInfoNancy.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Carte Leaflet</title>

    <!-- CSS de Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        #map {
            height: 600px;
            width: 100%;
        }
    </style>
</head>
<body>

<h2>Carte Leaflet</h2>
<div id="map"></div>

<!-- JS de Leaflet -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    var lat = <?php echo $lat; ?>;
    var lng = <?php echo $lng; ?>;
    var geojson = <?php echo json_encode($geojson, JSON_UNESCAPED_UNICODE); ?>;

    var map = L.map('map').setView([lat, lng], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    L.marker([lat, lng]).addTo(map)
        .bindPopup("Votre position : " + lat + ", " + lng)
        .openPopup();

    L.geoJSON(geojson, {
        style: feature => {
            // Couleur selon type
            switch (feature.properties.type) {
                case 'CONSTRUCTION': return { color: 'red' };
                default: return { color: 'blue' };
            }
        },
        pointToLayer: (feature, latlng) => {
            return L.circleMarker(latlng, {
                radius: 6,
                fillOpacity: 0.8
            });
        },
        onEachFeature: (feature, layer) => {
            layer.bindPopup(`
          <strong>${feature.properties.type}</strong><br>
          ${feature.properties.description ?? ''}<br>
          <em>${feature.properties.street ?? ''}</em><br><br>
          <b>Début : ${feature.properties.starttime ?? ''}</b><br>
          <b>Fin : ${feature.properties.endtime ?? ''}</b>
        `);
        }
    }).addTo(map);
</script>

</body>
</html>