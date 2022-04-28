<?php

declare(strict_types=1);

namespace Duffel\Examples;

require_once __DIR__ . '/../vendor/autoload.php';

use Duffel\Client;

$client = new Client();
$client->setAccessToken(getenv('DUFFEL_ACCESS_TOKEN'));

echo "Duffel Flights API - exploring data example\n";
$start = time();

echo "Loading aircraft...\n";

$allAircraft = $client->aircraft()->all();

echo sprintf("Got %d aircraft\n", count($allAircraft));

echo sprintf("Found aircraft %s\n", $allAircraft[0]['name']);

$singleAircraft = $client->aircraft()->show($allAircraft[0]['id']);

echo sprintf("Aircraft's IATA code is %s\n", $singleAircraft['iata_code']);

echo "Loading airlines...\n";

$airlines = $client->airlines()->all();

echo sprintf("Got %d airlines\n", count($airlines));

echo sprintf("Found airline %s\n", $airlines[0]['name']);

$airline = $client->airlines()->show($airlines[0]['id']);

echo sprintf("Airline's IATA code is %s\n", $airline['iata_code']);

$finish = time();
echo sprintf("Finished in %d seconds.\n", ($finish - $start));
