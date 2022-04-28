<?php

declare(strict_types=1);

namespace Duffel\Tests;

use Duffel\Api\Aircraft;
use Duffel\Api\Airlines;
use Duffel\Api\Airports;
use Duffel\Api\OfferRequests;
use Duffel\Api\Offers;
use Duffel\Client;
use Duffel\Exception\InvalidAccessTokenException;
use Duffel\HttpClient\Builder;
use Http\Client\Common\HttpMethodsClient;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase {
  private $subject;

  public function setUp(): void {
    $this->subject = new Client(
      new Builder(),
    );
  }

  public function testCreatesNewClient(): void {
    $this->assertInstanceOf(Client::class, $this->subject);
    $this->assertInstanceOf(HttpMethodsClient::class, $this->subject->getHttpClient());
  }

  public function testNewSetsDefaultApiVersion(): void {
    $this->assertSame('beta', $this->subject->getApiVersion());
  }

  public function testNewSetsDefaultAccessToken(): void {
    $this->assertSame(null, $this->subject->getAccessToken());
  }

  public function testAircraftUsesApiClass(): void {
    $this->assertInstanceOf(Aircraft::class, $this->subject->aircraft());
  }

  public function testAirlinesUsesApiClass(): void {
    $this->assertInstanceOf(Airlines::class, $this->subject->airlines());
  }

  public function testAirportsUsesApiClass(): void {
    $this->assertInstanceOf(Airports::class, $this->subject->airports());
  }

  public function testOfferRequestsUsesApiClass(): void {
    $this->assertInstanceOf(OfferRequests::class, $this->subject->offer_requests());
  }

  public function testOffersUsesApiClass(): void {
    $this->assertInstanceOf(Offers::class, $this->subject->offers());
  }

  public function testSetAccessTokenChangesValue(): void {
    $this->subject->setAccessToken('some-token');

    $this->assertSame('some-token', $this->subject->getAccessToken());
  }

  public function testSetAccessTokenWithEmptyStringThrowsException(): void {
    $this->expectException(InvalidAccessTokenException::class);
      
    $this->subject->setAccessToken('   ');
  }

  public function testSetAccessTokenWithNullThrowsTypeError(): void {
    $this->expectException(\TypeError::class);
      
    $this->subject->setAccessToken(null);
  }

  public function testSetApiVersionChangesValue(): void {
    $this->subject->setApiVersion('some-version');

    $this->assertSame('some-version', $this->subject->getApiVersion());
  }
}
