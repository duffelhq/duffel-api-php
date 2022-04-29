<?php

declare(strict_types=1);

namespace Duffel\Tests\Api;

use Duffel\Api\OrderChanges;
use Duffel\Client;
use Http\Client\Common\HttpMethodsClientInterface;
use PHPUnit\Framework\TestCase;

class OrderChangesTest extends TestCase {
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
                 $this->equalTo('/air/order_changes'),
                 $this->equalTo(['Content-Type' => 'application/json']),
                 $this->equalTo('{"data":{"selected_order_change_offer":"some-id"}}'),
               );

    $actual = new OrderChanges($this->stub);
    $actual->create('some-id');
  }

  public function testConfirmWithIdAndPaymentCallsGetWithExpectedUri(): void {
    $this->mock->expects($this->once())
               ->method('post')
               ->with(
                 $this->equalTo('/air/order_changes/some-id/actions/confirm'),
                 $this->equalTo(['Content-Type' => 'application/json']),
                 $this->equalTo('{"data":{"payment":{"type":"balance","currency":"GBP","amount":"30.20"}}}'),
               );

    $actual = new OrderChanges($this->stub);
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
                 $this->equalTo('/air/order_changes/some-id'),
                 $this->equalTo([]),
               );

    $actual = new OrderChanges($this->stub);
    $actual->show('some-id');
  }

}
