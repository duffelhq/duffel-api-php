<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Duffel\Client;

echo "Duffel Flights API - book hold order example\n";
$start = time();

$client = new Client();
$client->setAccessToken(getenv('DUFFEL_ACCESS_TOKEN'));

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

$order = $client->orders()->create(array(
  "selected_offers" => array($pricedOffer["id"]),
  "type" => "hold",
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

echo sprintf("Created hold order %s with booking reference %s\n", $order["id"], $order["booking_reference"]);
echo sprintf("This order must be cancelled or paid for by %s\n", $order["payment_status"]["payment_required_by"]);

$payment = $client->payments()->create($order["id"], array(
  "amount" => $order["total_amount"],
  "currency" => $order["total_currency"],
  "type" => "balance",
));

echo sprintf("Paid for hold order %s with payment reference %s\n", $order["id"], $payment["id"]);

$orderCancellation = $client->orderCancellations()->create($order["id"]);

echo sprintf("Requested refund quote for order %s – %s (%s) is available\n", $order["id"], $orderCancellation["refund_amount"], $orderCancellation["refund_currency"]);

$client->orderCancellations()->confirm($orderCancellation["id"]);

echo sprintf("Confirmed refund quote for order %s\n", $order["id"]);

$finish = time();
echo sprintf("Finished in %d seconds.\n", ($finish - $start));
