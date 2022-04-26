<?php

declare(strict_types=1);

namespace Duffel\Tests;

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
