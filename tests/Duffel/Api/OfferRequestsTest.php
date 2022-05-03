<?php

declare(strict_types=1);

namespace Duffel\Tests\Api;

use Duffel\Api\OfferRequests;
use Duffel\Client;
use Duffel\Exception\RuntimeException;
use Http\Client\Common\HttpMethodsClientInterface;
use PHPUnit\Framework\TestCase;

class OfferRequestsTest extends TestCase {
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
               ->with($this->equalTo('/air/offer_requests'));

    $actual = new OfferRequests($this->stub);
    $actual->all();
  }

  public function testCreateWithoutParametersRaisesException(): void {
    $this->expectException(RuntimeException::class);

    $actual = new OfferRequests($this->stub);
    $actual->create();
  }

  public function testCreateWithCabinClassParameterRaisesException(): void {
    $this->expectException(RuntimeException::class);

    $actual = new OfferRequests($this->stub);
    $actual->create('business');
  }

  public function testCreateWithEconomyCabinClassAndPassengersRaisesException(): void {
    $this->expectException(RuntimeException::class);

    $actual = new OfferRequests($this->stub);
    $actual->create('economy', [['given_name' => 'Amelia', 'family_name' => 'Earhart', 'age' => 30]]);
  }

  public function testCreateWithEconomyCabinClassAndPassengersAndSlicesCallsPostWithExpectedUriAndBody(): void {
    $this->mock->expects($this->once())
               ->method('post')
               ->with(
                 $this->equalTo('/air/offer_requests'),
                 ['Content-Type' => 'application/json'],
                 $this->equalTo('{"data":{"cabin_class":"economy","passengers":[{"given_name":"Amelia","family_name":"Earhart","age":30}],"slices":[{"origin":"LHR","destination":"JFK","departure_date":"2022-05-20"}]}}')
               );

    $actual = new OfferRequests($this->stub);
    $actual->create(
      'economy', 
      [['given_name' => 'Amelia', 'family_name' => 'Earhart', 'age' => 30]],
      [['origin' => 'LHR', 'destination' => 'JFK', 'departure_date' => '2022-05-20']]
    );
  }

  public function testShowCallsGetWithExpectedUri(): void {
    $this->mock->expects($this->once())
               ->method('get')
               ->with($this->equalTo('/air/offer_requests/some-id'));

    $actual = new OfferRequests($this->stub);
    $actual->show('some-id');
  }
}
