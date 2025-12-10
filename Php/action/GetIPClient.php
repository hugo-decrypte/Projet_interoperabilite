<?php

function getClientIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    }
    return $_SERVER['REMOTE_ADDR'];
}

$ip = getClientIP();

// 1. API ip-api
$geoUrl = "http://ip-api.com/json/" . $ip;
$file = file_get_contents($geoUrl);
if(!$file) {
    //ip local
    $ip = "193.50.135.199";
    $file = file_get_contents($geoUrl);
}
$geoData = json_decode($file, true);

$coords = [];

if ($geoData && $geoData["status"] === "success" && $geoData["city"] === "Nancy") {
    $coords['lat'] = $geoData["lat"];
    $coords['lon'] = $geoData["lon"];
    $locationSource = "IP déjà localisée à Nancy.";
} else {

    // 2. API Nominatim OpenStreetMap (si pas à Nancy)
    $opts = array(
        'http' => array(
            'method' => "GET",
            'header' => "User-Agent: PHP-App/1.0\r\n"
        ),
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false
        )
    );

    $context = stream_context_create($opts);

    $iutUrl = "https://nominatim.openstreetmap.org/search?q=IUT+Charlemagne+Nancy&format=jsonv2&limit=1";
    $iutData = json_decode(file_get_contents($iutUrl, false, $context), true);

    if (!empty($iutData)) {
        $coords['lat'] = $iutData[0]['lat'];
        $coords['lon'] = $iutData[0]['lon'];
        $locationSource = "Votre IP n'est pas à Nancy → coordonnées de l’IUT Charlemagne affichées.";
    } else {
        $coords['lat'] = "";
        $coords['lon'] = "";
        $locationSource = "Y a rien mon pote, ça marche pas";
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Localisation IP</title>
</head>
<body>

<h1>Résultat de la localisation</h1>

<div class="box">
    <p><strong>Adresse IP détectée :</strong> <?= htmlspecialchars($ip) ?></p>
    <p><strong>Message :</strong> <?= htmlspecialchars($locationSource) ?></p>
    <p><strong>Latitude :</strong> <?= htmlspecialchars($coords['lat']) ?></p>
    <p><strong>Longitude :</strong> <?= htmlspecialchars($coords['lon']) ?></p>
</div>

</body>
</html>
