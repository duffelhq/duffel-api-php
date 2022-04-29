<?php

declare(strict_types=1);

namespace Duffel\Tests\Api;

use Duffel\Api\Payments;
use Duffel\Client;
use Http\Client\Common\HttpMethodsClientInterface;
use PHPUnit\Framework\TestCase;

class PaymentsTest extends TestCase {
  private $mock;
  private $stub;

  public function setUp(): void {
    $this->mock = $this->createMock(HttpMethodsClientInterface::class);
    $this->stub = $this->createStub(Client::class);
    $this->stub->method('getHttpClient')
               ->willReturn($this->mock);
  }
  public function testCreateWithIdCallsGetWithExpectedUri(): void {
    $this->mock->expects($this->once())
               ->method('post')
               ->with(
                 $this->equalTo('/air/payments'),
                 $this->equalTo(['Content-Type' => 'application/json']),
                 $this->equalTo('{"data":{"order_id":"some-order-id","payment":{"amount":"30.20","currency":"GBP","type":"balance"}}}'),
               );

    $actual = new Payments($this->stub);
    $actual->create('some-order-id', [
      'amount' => '30.20',
      'currency' => 'GBP',
      'type' => 'balance',
    ]);
  }
}
