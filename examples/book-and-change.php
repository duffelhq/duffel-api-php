<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Duffel\Client;

echo "Duffel Flights API - search and book example\n";
$start = time();

$client = new Client;
$client->setAccessToken(getenv('DUFFEL_ACCESS_TOKEN'));

$departureDate = (new DateTime)->add(new DateInterval("P10D"))->format('Y-m-d');

$offerRequest = $client->offerRequests()->create(
  "economy", 
  array(
    array("age" => 25)
  ),
  array(
    array(
      "origin" => "LHR",
      "destination" => "STN", 
      "departure_date" => $departureDate
    )
  )
);

echo sprintf("Created offer request: %s\n", $offerRequest["id"]);

$offers = $client->offers()->all($offerRequest["id"]);

echo sprintf("Got %s offers\n", count($offers));

$selectedOffer = array_shift($offers);

echo sprintf("Selected offer %s to book\n", $selectedOffer["id"]);

$pricedOffer = $client->offers()->show($selectedOffer["id"]);

echo sprintf("The final price for offer %s is %s (%s)\n", $pricedOffer["id"],
  $pricedOffer["total_amount"],
  $pricedOffer["total_currency"]
);

$order = $client->orders()->create(array(
  "selected_offers" => array($pricedOffer["id"]),
  "payments" => array(
    array(
      "type" => "balance",
      "amount" => $pricedOffer["total_amount"],
      "currency" => $pricedOffer["total_currency"]
    )
  ),
  "passengers" => array(
    array(
      "id" => $pricedOffer["passengers"][0]["id"],
      "title" => "mr",
      "gender" => "m",
      "given_name" => "Zepp",
      "family_name" => "Lin",
      "born_on" => "1990-01-26",
      "phone_number" => "+441234567890",
      "email" => "z.e.l@zeppelin.aero",
    )
  )
));

echo sprintf("Created order %s with booking reference %s\n", $order["id"], $order["booking_reference"]);

$orderChangeRequest = $client->orderChangeRequests()->create($order["id"],
  array(
    "add" => array(
      array(
        "cabin_class" => "economy",
        "departure_date" => (new DateTime)->add(new DateInterval("P10D"))->format('Y-m-d'),
        "origin" => "LHR",
        "destination" => "STN",
      )
    ),
    "remove" => array(
      array(
        "slice_id" => $order["slices"][0]["id"],
      )
    ),
  )
);

$orderChangeOffers = $client->orderChangeOffers()->all($orderChangeRequest["id"]);

echo sprintf("Got %d options for changing the order, picking the first option\n", count($orderChangeOffers));

$orderChange = $client->orderChanges()->create($orderChangeOffers[0]["id"]);

echo sprintf("Created order change %s, confirming...\n", $orderChange["id"]);

$client->orderChanges()->confirm($orderChange["id"],
  array(
    "type" => "balance",
    "amount" => $orderChange["change_total_amount"],
    "currency" => $orderChange["change_total_currency"],
  ),
);

echo sprintf("Processed change to order %s, costing %s (%s)\n", $order["id"], $orderChange["change_total_amount"], $orderChange["change_total_currency"]);

$finish = time();
echo sprintf("Finished in %d seconds.\n", ($finish - $start));
