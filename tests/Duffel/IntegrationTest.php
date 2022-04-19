<?php

declare(strict_types=1);

namespace Duffel\Tests;

use Duffel\Client;
use Duffel\Exception\RuntimeException;
use PHPUnit\Framework\TestCase;

class IntegrationTest extends TestCase {
  public function testOfferRequestCreate(): void {
    $client = new Client();

    $response = $client->offerRequests()->create();

    $this->assertIsArray($response);
  }

  public function testOfferRequestNotFound(): void {
    $client = new Client();

    $this->expectException(RuntimeException::class);
    $this->expectExceptionMessage('Not Found');

    $response = $client->offerRequests()->show("1");
  }
}
