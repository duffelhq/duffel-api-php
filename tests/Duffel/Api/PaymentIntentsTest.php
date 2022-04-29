<?php

declare(strict_types=1);

namespace Duffel\Tests\Api;

use Duffel\Api\PaymentIntents;
use Duffel\Client;
use Http\Client\Common\HttpMethodsClientInterface;
use PHPUnit\Framework\TestCase;

class PaymentIntentsTest extends TestCase {
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
                 $this->equalTo('/payments/payment_intents'),
                 $this->equalTo(['Content-Type' => 'application/json']),
                 $this->equalTo('{"data":{"amount":"30.20","currency":"GBP"}}'),
               );

    $actual = new PaymentIntents($this->stub);
    $actual->create([
      'amount' => '30.20',
      'currency' => 'GBP',
    ]);
  }

  public function testConfirmWithIdAndPaymentCallsGetWithExpectedUri(): void {
    $this->mock->expects($this->once())
               ->method('post')
               ->with(
                 $this->equalTo('/payments/payment_intents/some-id/actions/confirm'),
                 $this->equalTo([]),
                 $this->equalTo(null),
               );

    $actual = new PaymentIntents($this->stub);
    $actual->confirm('some-id', [
      'type' => 'balance',
      'currency' => 'GBP',
      'amount' => '30.20',
    ]);
  }

  public function testShowWithIdCallsGetWithExpectedUri(): void {
    $this->mock->expects($this->once())
               ->method('get')
               ->with(
                 $this->equalTo('/payments/payment_intents/some-id'),
                 $this->equalTo([]),
               );

    $actual = new PaymentIntents($this->stub);
    $actual->show('some-id');
  }

}
