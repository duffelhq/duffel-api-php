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

$selectedOffer = $offers[0];

echo sprintf("Selected offer %s to book\n", $selectedOffer["id"]);

$pricedOffer = $client->offers()->show($selectedOffer["id"]);

echo sprintf("The final price for offer %s is %s (%s)\n", $pricedOffer["id"],
  $pricedOffer["total_amount"],
  $pricedOffer["total_currency"]
);

$seatMaps = $client->seatMaps()->all($pricedOffer["id"]);

$availableSeat = array_filter($seatMaps, function ($seatMap) {
  foreach($seatMap["cabins"] as $cabin) {
    foreach($cabin["rows"] as $row) {
      foreach($row["sections"] as $section) {
        foreach($section["elements"] as $element) {
          return $element["type"] === "seat" && is_array($element["available_services"]);
        }
      }
    }
  }
});
$availableSeatService = $availableSeat[0];

echo sprintf("Adding seat %s costing %s (%s)\n", $availableSeatService["designator"],
  $availableSeatService["total_amount"],
  $availableSeatService["total_currency"],
);

$totalAmount = round(($pricedOffer["total_amount"] + $availableSeatService["total_amount"]), 2);

echo sprintf("Total amount is %s\n", $totalAmount);

$order = $client->orders()->create(array(
  "selected_offers" => array($pricedOffer["id"]),
  "services" => array(
    array(
      "id" => $availableSeatService["id"],
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
