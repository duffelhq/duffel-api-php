<?php

declare(strict_types=1);

namespace Duffel\Tests\Api;

use Duffel\Api\Orders;
use Duffel\Client;
use Http\Client\Common\HttpMethodsClientInterface;
use PHPUnit\Framework\TestCase;

class OrdersTest extends TestCase {
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
               ->with($this->equalTo('/air/orders'));

    $actual = new Orders($this->stub);
    $actual->all();
  }

  public function testShowWithIdCallsGetWithExpectedUri(): void {
    $this->mock->expects($this->once())
               ->method('get')
               ->with($this->equalTo('/air/orders/some-id'));

    $actual = new Orders($this->stub);
    $actual->show('some-id');
  }

  public function testCreateWithOfferAndPassengersAndPaymentsCallsGetWithExpectedUri(): void {
    $this->mock->expects($this->once())
               ->method('post')
               ->with(
                 $this->equalTo('/air/orders'),
                 $this->equalTo(['Content-Type' => 'application/json']),
                 $this->equalTo('{"data":{"selected_offers":"off_00009htyDGjIfajdNBZRlw","payments":[{"type":"balance","amount":"30.20","currency":"GBP"}],"passengers":[{"id":"pas_00009hj8USM7Ncg31cBCLL","title":"ms","gender":"f","given_name":"Amelia","family_name":"Earhart","born_on":"1987-07-24","phone_number":"+442080160509","email":"amelia@duffel.dev"}]}}')
               );

    $actual = new Orders($this->stub);
    $actual->create(
      [
        'selected_offers' => 'off_00009htyDGjIfajdNBZRlw',
        'payments' => [
          [
            'type' => 'balance',
            'amount' => '30.20',
            'currency' => 'GBP'
          ]
        ],
        'passengers' => [
          [
            'id' => 'pas_00009hj8USM7Ncg31cBCLL',
            'title' => 'ms',
            'gender' => 'f',
            'given_name' => 'Amelia',
            'family_name' => 'Earhart',
            'born_on' => '1987-07-24',
            'phone_number' => '+442080160509',
            'email' => 'amelia@duffel.dev',
          ]
        ]
      ]
    );
  }

  public function testCreateWithOfferAndServicesAndPassengersAndPaymentsCallsGetWithExpectedUri(): void {
    $this->mock->expects($this->once())
               ->method('post')
               ->with(
                 $this->equalTo('/air/orders'),
                 $this->equalTo(['Content-Type' => 'application/json']),
                 $this->equalTo('{"data":{"selected_offers":"off_00009htyDGjIfajdNBZRlw","services":[{"id":"ase_00009hj8USM7Ncg31cB123","quantity":1}],"payments":[{"type":"balance","amount":"30.20","currency":"GBP"}],"passengers":[{"id":"pas_00009hj8USM7Ncg31cBCLL","title":"ms","gender":"f","given_name":"Amelia","family_name":"Earhart","born_on":"1987-07-24","phone_number":"+442080160509","email":"amelia@duffel.dev"}]}}')
               );

    $actual = new Orders($this->stub);
    $actual->create(
      [
        'selected_offers' => 'off_00009htyDGjIfajdNBZRlw',
        'services' => [
          [
            'id' => 'ase_00009hj8USM7Ncg31cB123',
            "quantity" => 1,
          ]
        ],
        'payments' => [
          [
            'type' => 'balance',
            'amount' => '30.20',
            'currency' => 'GBP'
          ]
        ],
        'passengers' => [
          [
            'id' => 'pas_00009hj8USM7Ncg31cBCLL',
            'title' => 'ms',
            'gender' => 'f',
            'given_name' => 'Amelia',
            'family_name' => 'Earhart',
            'born_on' => '1987-07-24',
            'phone_number' => '+442080160509',
            'email' => 'amelia@duffel.dev',
          ]
        ]
      ]
    );
  }
}
