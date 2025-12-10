<?php

require_once __DIR__ . "/../config/bootstrap.php";

echo <<<END
<a href="map.php">Accèder à la carte</a>
END;


require_once __DIR__ . "/../action/GetIPClient.php";

$apiURL = "https://www.infoclimat.fr/public-api/gfs/xml?_ll=48.67103,6.15083&_auth=ARsDFFIsBCZRfFtsD3lSe1Q8ADUPeVRzBHgFZgtuAH1UMQNgUTNcPlU5VClSfVZkUn8AYVxmVW0Eb1I2WylSLgFgA25SNwRuUT1bPw83UnlUeAB9DzFUcwR4BWMLYwBhVCkDb1EzXCBVOFQoUmNWZlJnAH9cfFVsBGRSPVs1UjEBZwNkUjIEYVE6WyYPIFJjVGUAZg9mVD4EbwVhCzMAMFQzA2JRMlw5VThUKFJiVmtSZQBpXGtVbwRlUjVbKVIuARsDFFIsBCZRfFtsD3lSe1QyAD4PZA%3D%3D&_c=19f3aa7d766b6ba91191c8be71dd1ab2";

$file = file_get_contents($apiURL);
file_put_contents("data.xml", $file);

$xml = simplexml_load_file("data.xml");
$xsl = simplexml_load_file("Météo.xsl");

$proc = new XSLTProcessor();
$proc->importStylesheet($xsl);

$html = $proc->transformToXML($xml);

echo $html;
