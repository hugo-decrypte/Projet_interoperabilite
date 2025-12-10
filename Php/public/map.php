<?php
$lat = 48.67103;
$lng = 6.15083;
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

    var map = L.map('map').setView([lat, lng], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    L.marker([lat, lng]).addTo(map)
        .bindPopup("Votre position : " + lat + ", " + lng)
        .openPopup();
</script>

</body>
</html>