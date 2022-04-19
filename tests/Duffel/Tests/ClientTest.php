<?php

declare(strict_types=1);

namespace Duffel\Tests;

use Duffel\Client;
use Http\Client\Common\HttpMethodsClient;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase {
  public function testCreatesAClient(): void {
    $client = new Client();

    $this->assertInstanceOf(Client::class, $client);
    $this->assertInstanceOf(HTTPMethodsClient::class, $client->getHttpClient());
  }

  public function testSetsAccessToken(): void {
    $client = new Client('some-access-token');
    $this->assertSame('some-access-token', $client->getAccessToken());
  }
}
