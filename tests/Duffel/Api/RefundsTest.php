<?php

declare(strict_types=1);

namespace Duffel\Tests\Api;

use Duffel\Api\Refunds;
use Duffel\Client;
use Http\Client\Common\HttpMethodsClientInterface;
use PHPUnit\Framework\TestCase;

class RefundsTest extends TestCase {
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
                 $this->equalTo('/payments/refunds'),
                 $this->equalTo(['Content-Type' => 'application/json']),
                 $this->equalTo('{"data":{"amount":"30.20","currency":"GBP","payment_intent_id":"pit_00009hthhsUZ8W4LxQgkjo"}}'),
               );

    $actual = new Refunds($this->stub);
    $actual->create([
      'amount' => '30.20',
      'currency' => 'GBP',
      'payment_intent_id' => 'pit_00009hthhsUZ8W4LxQgkjo',
    ]);
  }

  public function testShowWithIdCallsGetWithExpectedUri(): void {
    $this->mock->expects($this->once())
               ->method('get')
               ->with(
                 $this->equalTo('/payments/refunds/some-id'),
                 $this->equalTo([]),
               );

    $actual = new Refunds($this->stub);
    $actual->show('some-id');
  }

}
