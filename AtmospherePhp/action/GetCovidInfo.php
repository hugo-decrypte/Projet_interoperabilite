<?php

$apiURL = "https://www.data.gouv.fr/api/1/datasets/r/d2671c6c-c0eb-4e12-b69a-8e8f87fc224c";

$opts = array(
    'http' => array('proxy'=> 'tcp://127.0.0.1:8080', 'request_fulluri'=> true),
    'ssl' => array( 'verify_peer' => false, 'verify_peer_name' => false)
);

$context = stream_context_create($opts);

$jsonContent = file_get_contents($apiURL, false, $context);
$data = json_decode($jsonContent, true);

$dates = [];
$casConfirmes = [];
$deces = [];
$hospitalises = [];
$reanimation = [];
$gueris = [];

foreach ($data as $entry) {
    $dateObj = new DateTime($entry['date']);
    $dates[] = $dateObj->format('d/m/Y');

    $casConfirmes[] = $entry['casConfirmes'];
    $deces[] = $entry['deces'];
    $hospitalises[] = $entry['hospitalises'] ?? 0;
    $reanimation[] = $entry['reanimation'] ?? 0;
    $gueris[] = $entry['gueris'] ?? 0;
}