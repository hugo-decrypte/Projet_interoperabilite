<?php


// 1. Récupération IP client
function getClientIp() {
    //if (!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
    //if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    return $_SERVER['REMOTE_ADDR'];
}

$ip = getClientIp();

// 2. Appel API de géolocalisation
$geoUrl = "https://ip-api.com/#" . $ip;
$geoData = json_decode(file_get_contents($geoUrl), true);

// Si l'API échoue ou la ville n'est pas Nancy → prendre les coords de l'IUT
$coords = [];

if ($geoData["status"] === "success" && $geoData["city"] === "Nancy") {
    $coords['lat'] = $geoData["lat"];
    $coords['lon'] = $geoData["lon"];
    $locationSource = "IP déjà localisée à Nancy.";
} else {
    // 3. Récupération des coordonnées de l’IUT via Nominatim
    $iutUrl = "https://nominatim.openstreetmap.org/ui/search.html?q=IUT+Charlemagne,+Nancy&format=json&limit=1";
    $iutData = json_decode(file_get_contents($iutUrl), true);

    if (!empty($iutData)) {
        $coords['lat'] = $iutData[0]['lat'];
        $coords['lon'] = $iutData[0]['lon'];
        $locationSource = "Votre IP n'est pas à Nancy → coordonnées de l’IUT Charlemagne affichées.";
    } else {
        $locationSource = "bah là tu fais pas d'effort y a rien";
    }
}

// ----------------------
// 4. Génération HTML
// ----------------------
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
    <p><strong>Latitude :</strong> <?= $coords['lat'] ?></p>
    <p><strong>Longitude :</strong> <?= $coords['lon'] ?></p>
</div>

</body>
</html>
