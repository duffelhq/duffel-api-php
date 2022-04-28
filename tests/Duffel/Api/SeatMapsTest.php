<?php

declare(strict_types=1);

namespace Duffel\Tests\Api;

use Duffel\Api\SeatMaps;
use Duffel\Client;
use Http\Client\Common\HttpMethodsClientInterface;
use PHPUnit\Framework\TestCase;

class SeatMapsTest extends TestCase {
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
                 $this->equalTo('/air/seat_maps?offer_id=some-offer-id'),
                 $this->equalTo([]),
               );

    $actual = new SeatMaps($this->stub);
    $actual->all('some-offer-id');
  }
}
