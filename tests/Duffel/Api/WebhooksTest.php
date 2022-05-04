<?php

declare(strict_types=1);

namespace Duffel\Tests\Api;

use Duffel\Api\Webhooks;
use Duffel\Client;
use Http\Client\Common\HttpMethodsClientInterface;
use PHPUnit\Framework\TestCase;

class WebhooksTest extends TestCase {
  private $mock;
  private $stub;

  public function setUp(): void {
    $this->mock = $this->createMock(HttpMethodsClientInterface::class);
    $this->stub = $this->createStub(Client::class);
    $this->stub->method('getHttpClient')
               ->willReturn($this->mock);
  }

  public function testCreateCallsPostWithExpectedUriAndPayload(): void {
    $this->mock->expects($this->once())
               ->method('post')
               ->with(
                 $this->equalTo('/air/webhooks'),
                 $this->equalTo(['Content-Type' => 'application/json']),
                 $this->equalTo('{"data":{"events":["event.one","event.two"],"url":"https://some-url.com/path"}}'),
               );

    $actual = new Webhooks($this->stub);
    $actual->create('https://some-url.com/path', ['event.one', 'event.two']);
  }

  public function testPingCallsPostWithExpectedUriAndPayload(): void {
    $this->mock->expects($this->once())
               ->method('post')
               ->with(
                 $this->equalTo('/air/webhooks/some-id/actions/ping'),
                 $this->equalTo([]),
                 $this->equalTo(null),
               );

    $actual = new Webhooks($this->stub);
    $actual->ping('some-id');
  }

  public function testUpdateCallsPostWithExpectedUriAndPayload(): void {
    $this->mock->expects($this->once())
               ->method('post')
               ->with(
                 $this->equalTo('/air/webhooks/some-id'),
                 $this->equalTo(['Content-Type' => 'application/json']),
                 $this->equalTo('{"data":{"active":true}}'),
               );

    $actual = new Webhooks($this->stub);
    $actual->update('some-id', true);
  }
}
