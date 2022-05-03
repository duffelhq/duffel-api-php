<?php

declare(strict_types=1);

namespace Duffel\Tests\Api;

use Duffel\Api\OrderChangeRequests;
use Duffel\Client;
use Http\Client\Common\HttpMethodsClientInterface;
use PHPUnit\Framework\TestCase;

class OrderChangeRequestsTest extends TestCase {
  private $mock;
  private $stub;

  public function setUp(): void {
    $this->mock = $this->createMock(HttpMethodsClientInterface::class);
    $this->stub = $this->createStub(Client::class);
    $this->stub->method('getHttpClient')
               ->willReturn($this->mock);
  }

  public function testCreateWithIdAndSlicesCallsGetWithExpectedUri(): void {
    $this->mock->expects($this->once())
               ->method('post')
               ->with(
                 $this->equalTo('/air/order_change_requests'),
                 $this->equalTo(['Content-Type' => 'application/json']),
                 $this->equalTo('{"data":{"slices":{"remove":[{"slice_id":"sli_00009htYpSCXrwaB9Dn123"}],"add":[{"origin":"LHR","destination":"JFK","departure_date":"2022-05-20","cabin_class":"economy"}]},"order_id":"ord_0000A3bQ8FJIQoEfuC07n6"}}')
               );

    $actual = new OrderChangeRequests($this->stub);
    $actual->create('ord_0000A3bQ8FJIQoEfuC07n6', [
      'remove' => [
        [
          'slice_id' => 'sli_00009htYpSCXrwaB9Dn123',
        ],
      ],
      'add' => [
        [
          'origin' => 'LHR',
          'destination' => 'JFK',
          'departure_date' => '2022-05-20',
          'cabin_class' => 'economy',
        ],
      ],
    ]);
  }

  public function testShowWithIdCallsGetWithExpectedUri(): void {
    $this->mock->expects($this->once())
               ->method('get')
               ->with($this->equalTo('/air/order_change_requests/some-id'));

    $actual = new OrderChangeRequests($this->stub);
    $actual->show('some-id');
  }
}
