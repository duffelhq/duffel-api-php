<?php

declare(strict_types=1);

namespace Duffel\Tests\Api;

use Duffel\Api\OrderChangeOffers;
use Duffel\Client;
use Http\Client\Common\HttpMethodsClientInterface;
use PHPUnit\Framework\TestCase;

class OrderChangeOffersTest extends TestCase {
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
               ->with(
                 $this->equalTo('/air/order_change_offers'),
                 $this->equalTo([]),
               );

    $actual = new OrderChangeOffers($this->stub);
    $actual->all();
  }

  public function testAllWithOrderChangeRequestIdCallsGetWithExpectedUri(): void {
    $this->mock->expects($this->once())
               ->method('get')
               ->with(
                 $this->equalTo('/air/order_change_offers?order_change_request_id=ocr_0000A3bQP9RLVfNUcdpLpw'),
                 $this->equalTo([]),
               );

    $actual = new OrderChangeOffers($this->stub);
    $actual->all('ocr_0000A3bQP9RLVfNUcdpLpw');
  }

  public function testShowWithIdCallsGetWithExpectedUri(): void {
    $this->mock->expects($this->once())
               ->method('get')
               ->with(
                 $this->equalTo('/air/order_change_offers/some-id'),
                 $this->equalTo([]),
               );

    $actual = new OrderChangeOffers($this->stub);
    $actual->show('some-id');
  }
}

