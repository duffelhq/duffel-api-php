<?php

declare(strict_types=1);

namespace Duffel\Tests;

use Duffel\WebhookEvent;
use PHPUnit\Framework\TestCase;

class WebhookEventTest extends TestCase {
  private $requestBody;
  private $requestSignature;
  private $webhookSecret;

  public function setUp(): void {
    $this->requestBody = '{"created_at":"2022-01-08T18:44:56.129339Z","data":{"changes":{},"object":{}},' .
        '"id":"eve_0000AFEsrBKZAcKgGtZCnQ","live_mode":false,"object":"order","type":"' . 
        'order.updated"}';
    $this->requestSignature = 't=1641667496,v1=691f25ffb1f206c0fda5bb7b1a9d60fafe42c5f42819d44a06a7cfe09486f102';
    $this->webhookSecret = 'a_secret';
  }

  public function testIsGenuineWhenInputIsValid(): void {
    $this->assertTrue(
      WebhookEvent::isGenuine($this->requestBody,
                              $this->requestSignature,
                              $this->webhookSecret)
    );
  }

  public function testIsGenuineWhenRequestSignatureIsInvalid(): void {
    $requestSignature = 'nah';

    $this->assertFalse(
      WebhookEvent::isGenuine($this->requestBody,
                              $requestSignature,
                              $this->webhookSecret)
    );
  }

  public function testIsGenuineWhenRequestBodyDoesNotMatchSignature(): void {
    $requestBody = 'foo';

    $this->assertFalse(
      WebhookEvent::isGenuine($requestBody,
                              $this->requestSignature,
                              $this->webhookSecret)
    );
  }
}
