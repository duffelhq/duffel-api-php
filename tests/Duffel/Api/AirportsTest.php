<?php

declare(strict_types=1);

namespace Duffel\Tests\Api;

use Duffel\Api\Airports;
use Duffel\Client;
use Http\Client\Common\HttpMethodsClientInterface;
use PHPUnit\Framework\TestCase;

class AirportsTest extends TestCase {
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
               ->with($this->equalTo('/air/airports'));

    $actual = new Airports($this->stub);
    $actual->all();
  }

  public function testShowCallsGetWithExpectedUri(): void {
    $this->mock->expects($this->once())
               ->method('get')
               ->with($this->equalTo('/air/airports/some-id'));

    $actual = new Airports($this->stub);
    $actual->show('some-id');
  }
}
