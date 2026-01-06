<?php

$apiCirculation = "https://carto.g-ny.eu/data/cifs/cifs_waze_v2.json";

$opts = array(
    'http' => array('proxy'=> 'tcp://127.0.0.1:8080', 'request_fulluri'=> true),
    'ssl' => array( 'verify_peer' => false, 'verify_peer_name' => false)
);

$context = stream_context_create($opts);

$json = file_get_contents($apiCirculation, false, $context);

if ($json === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Impossible de récupérer le flux']);
    exit;
}

$data = json_decode($json, true);
$incidents = $data['incidents'] ?? [];

foreach ($incidents as $incident) {

    $polyline = $incident['location']['polyline'] ?? '';
    if (!$polyline) continue;

    // Conversion "lat lon lat lon" → tableau
    $coords = array_map('floatval', explode(' ', $polyline));

    if (count($coords) === 2) {
        // Point
        $geometry = [
            'type' => 'Point',
            'coordinates' => [$coords[1], $coords[0]] // GeoJSON = lng, lat
        ];
    } else {
        // Ligne
        $line = [];
        for ($i = 0; $i < count($coords); $i += 2) {
            $line[] = [$coords[$i+1], $coords[$i]];
        }

        $geometry = [
            'type' => 'LineString',
            'coordinates' => $line
        ];
    }

    $incident['starttime'] = date('d/m/Y', strtotime($incident['starttime']));
    $incident['endtime'] = date('d/m/Y', strtotime($incident['endtime']));

    $geojson['features'][] = [
        'type' => 'Feature',
        'geometry' => $geometry,
        'properties' => [
            'id' => $incident['id'] ?? null,
            'type' => $incident['type'] ?? null,
            'description' => $incident['description'] ?? null,
            'street' => $incident['location']['street'] ?? null,
            'starttime' => $incident['starttime'],
            'endtime' => $incident['endtime']
        ]
    ];
}
