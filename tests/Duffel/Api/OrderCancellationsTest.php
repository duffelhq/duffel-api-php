<?php

declare(strict_types=1);

namespace Duffel\Tests\Api;

use Duffel\Api\OrderCancellations;
use Duffel\Client;
use Http\Client\Common\HttpMethodsClientInterface;
use PHPUnit\Framework\TestCase;

class OrderCancellationsTest extends TestCase {
  private $mock;
  private $stub;

  public function setUp(): void {
    $this->mock = $this->createMock(HttpMethodsClientInterface::class);
    $this->stub = $this->createStub(Client::class);
    $this->stub->method('getHttpClient')
               ->willReturn($this->mock);
  }

  public function testAllCallsGetWithExpectedUri(): void {
    $this->mock->expects($this->once())
               ->method('get')
               ->with($this->equalTo('/air/order_cancellations'));

    $actual = new OrderCancellations($this->stub);
    $actual->all();
  }

  public function testShowWithIdCallsGetWithExpectedUri(): void {
    $this->mock->expects($this->once())
               ->method('get')
               ->with($this->equalTo('/air/order_cancellations/some-id'));

    $actual = new OrderCancellations($this->stub);
    $actual->show('some-id');
  }

  public function testCreateWithIdCallsGetWithExpectedUri(): void {
    $this->mock->expects($this->once())
               ->method('post')
               ->with(
                 $this->equalTo('/air/order_cancellations'),
                 $this->equalTo(['Content-Type' => 'application/json']),
                 $this->equalTo('{"data":{"order_id":"some-id"}}'),
               );

    $actual = new OrderCancellations($this->stub);
    $actual->create('some-id');
  }

  public function testConfirmWithIdCallsGetWithExpectedUri(): void {
    $this->mock->expects($this->once())
               ->method('post')
               ->with(
                 $this->equalTo('/air/order_cancellations/some-id/actions/confirm'),
                 $this->equalTo([]),
                 $this->equalTo(null),
               );

    $actual = new OrderCancellations($this->stub);
    $actual->confirm('some-id');
  }
}
