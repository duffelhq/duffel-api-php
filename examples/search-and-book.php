<?php

declare(strict_types=1);

require("./vendor/autoload.php");

use Decimal\Decimal;
use Duffel\Client;

$client = new Client;

$departureDate = (new DateTime)->add(new DateInterval("P10D"))->format('Y-m-d');

$offerRequest = $client->offerRequests()->create(
  "economy", 
  array(
    array("type" => "adult")
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

$pricedOffer = $client->offers()->show($selectedOffer["id"], true);

echo sprintf("The final price for offer %s is %s (%s)\n", $pricedOffer["id"],
  $pricedOffer["total_amount"],
  $pricedOffer["total_currency"]
);

$availableService = array_shift($pricedOffer["available_services"]);

echo sprintf("Adding an extra bag with service %s costing %s (%s)\n", $availableService["id"],
  $availableService["total_amount"],
  $availableService["total_currency"],
);

$totalAmount = round(($pricedOffer["total_amount"] + $availableService["total_amount"]), 2);

echo sprintf("Total amount is %s\n", $totalAmount);

$order = $client->orders()->create(array(
  "selected_offers" => array($pricedOffer["id"]),
  "services" => array(
    array(
      "id" => $availableService["id"],
      "quantity" => 1,
    )
  ),
  "payments" => array(
    array(
      "type" => "balance",
      "amount" => $totalAmount,
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

$orderCancellation = $client->orderCancellations()->create($order["id"]);

echo sprintf("Requested refund quote for order %s – %s (%s) is available\n", $order["id"], $orderCancellation["refund_amount"], $orderCancellation["refund_currency"]);

$client->orderCancellations()->confirm($orderCancellation["id"]);

echo sprintf("Confirmed refund quote for order %s\n", $order["id"]);
