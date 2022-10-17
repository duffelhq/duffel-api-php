<?php

declare(strict_types=1);

namespace Duffel\Tests;

use Duffel\Api\Aircraft;
use Duffel\Api\Airlines;
use Duffel\Api\Airports;
use Duffel\Api\OfferRequests;
use Duffel\Api\Offers;
use Duffel\Api\OrderCancellations;
use Duffel\Api\OrderChangeOffers;
use Duffel\Api\OrderChangeRequests;
use Duffel\Api\OrderChanges;
use Duffel\Api\Orders;
use Duffel\Api\PaymentIntents;
use Duffel\Api\Payments;
use Duffel\Api\Refunds;
use Duffel\Api\SeatMaps;
use Duffel\Api\Webhooks;
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
    $this->assertSame('v1', $this->subject->getApiVersion());
  }

  public function testNewSetsDefaultAccessToken(): void {
    $this->assertSame('', $this->subject->getAccessToken());
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
    $this->assertInstanceOf(OfferRequests::class, $this->subject->offerRequests());
  }

  public function testOffersUsesApiClass(): void {
    $this->assertInstanceOf(Offers::class, $this->subject->offers());
  }

  public function testOrderCancellationsUsesApiClass(): void {
    $this->assertInstanceOf(OrderCancellations::class, $this->subject->orderCancellations());
  }

  public function testOrderChangeOffersUsesApiClass(): void {
    $this->assertInstanceOf(OrderChangeOffers::class, $this->subject->orderChangeOffers());
  }

  public function testOrderChangeRequestsUsesApiClass(): void {
    $this->assertInstanceOf(OrderChangeRequests::class, $this->subject->orderChangeRequests());
  }

  public function testOrderChangesUsesApiClass(): void {
    $this->assertInstanceOf(OrderChanges::class, $this->subject->orderChanges());
  }

  public function testOrdersUsesApiClass(): void {
    $this->assertInstanceOf(Orders::class, $this->subject->orders());
  }

  public function testPaymentIntentsUsesApiClass(): void {
    $this->assertInstanceOf(PaymentIntents::class, $this->subject->paymentIntents());
  }

  public function testPaymentsUsesApiClass(): void {
    $this->assertInstanceOf(Payments::class, $this->subject->payments());
  }

  public function testRefundsUsesApiClass(): void {
    $this->assertInstanceOf(Refunds::class, $this->subject->refunds());
  }

  public function testSeatMapsUsesApiClass(): void {
    $this->assertInstanceOf(SeatMaps::class, $this->subject->seatMaps());
  }

  public function testWebhooksUsesApiClass(): void {
    $this->assertInstanceOf(Webhooks::class, $this->subject->webhooks());
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
