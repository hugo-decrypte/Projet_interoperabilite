<?php

$apiURL = "https://www.data.gouv.fr/api/1/datasets/r/d2671c6c-c0eb-4e12-b69a-8e8f87fc224c";

$jsonContent = file_get_contents($apiURL);
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