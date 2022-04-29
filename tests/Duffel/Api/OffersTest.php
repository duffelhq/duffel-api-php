<?php

declare(strict_types=1);

namespace Duffel\Tests\Api;

use Duffel\Api\Offers;
use Duffel\Client;
use Http\Client\Common\HttpMethodsClientInterface;
use PHPUnit\Framework\TestCase;

class OffersTest extends TestCase {
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
               ->with($this->equalTo('/air/offers?offer_request_id=some-offer-request-id'));

    $actual = new Offers($this->stub);
    $actual->all('some-offer-request-id');
  }

  public function testShowWithIdCallsGetWithExpectedUri(): void {
    $this->mock->expects($this->once())
               ->method('get')
               ->with($this->equalTo('/air/offers/some-id'));

    $actual = new Offers($this->stub);
    $actual->show('some-id');
  }

  public function testShowWithIdAndFalseCallsGetWithExpectedUri(): void {
    $this->mock->expects($this->once())
               ->method('get')
               ->with($this->equalTo('/air/offers/some-id'));

    $actual = new Offers($this->stub);
    $actual->show('some-id', false);
  }

  public function testShowWithIdAndTrueReturnsAvailableServices(): void {
    $this->mock->expects($this->once())
               ->method('get')
               ->with($this->equalTo('/air/offers/some-id?return_available_services=true'));

    $actual = new Offers($this->stub);
    $actual->show('some-id', true);
  }

  public function testUpdateWithIdCallsGetWithExpectedUri(): void {
    $this->mock->expects($this->once())
               ->method('post')
               ->with(
                 $this->equalTo('/air/offers/some-id/passengers/some-offer-passenger-id'),
                 $this->equalTo(['Content-Type' => 'application/json']),
                 $this->equalTo('{"data":{"account_number":"12901014","airline_iata_code":"BA"}}'),
               );

    $actual = new Offers($this->stub);
    $actual->update(
      'some-id',
      'some-offer-passenger-id',
      'some-family-name',
      'some-given-name',
      ['account_number' => '12901014', 'airline_iata_code' => 'BA']
    );
  }
}
