<?php

declare(strict_types=1);

namespace Duffel\Examples;

require("./vendor/autoload.php");

use Duffel\Client;

$client = new Client();
$client->setAccessToken($_ENV['DUFFEL_ACCESS_TOKEN']);

echo "Duffel Flights API - book and change example\n";
$start = time();

echo "Loading aircraft...\n";

$allAircraft = $client->aircraft()->all();

echo sprintf("Got %d aircraft\n", count($allAircraft));

echo sprintf("Found aircraft %s\n", $allAircraft[0]['name']);

$singleAircraft = $client->aircraft()->show($allAircraft[0]['id']);

echo sprintf("Aircraft's IATA code is %s\n", $singleAircraft['iata_code']);

$finish = time();
echo sprintf("Finished in %d seconds.\n", ($finish - $start));
