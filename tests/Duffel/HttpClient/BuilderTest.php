<?php

declare(strict_types=1);

namespace Duffel\Tests\HttpClient;

use Duffel\HttpClient\Builder;
use Http\Client\Common\HttpMethodsClientInterface;
use Http\Client\Common\Plugin;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

class BuilderTest extends TestCase {
  private $subject;

  public function setUp(): void {
    $this->subject = new Builder(
      $this->createMock(ClientInterface::class),
      $this->createMock(RequestFactoryInterface::class),
      $this->createMock(StreamFactoryInterface::class),
      $this->createMock(UriFactoryInterface::class)
    );
  }

  public function testAddPluginShouldInvalidateHttpClient(): void {
    $client = $this->subject->getHttpClient();

    $this->subject->addPlugin($this->createMock(Plugin::class));

    $this->assertNotSame($client, $this->subject->getHttpClient());
  }

  public function testRemovePluginShouldInvalidateHttpClient(): void {
    $this->subject->addPlugin($this->createMock(Plugin::class));

    $client = $this->subject->getHttpClient();

    $this->subject->removePlugin(Plugin::class);

    $this->assertNotSame($client, $this->subject->getHttpClient());
  }

  public function testHttpClientShouldBeHttpMethodsClient(): void {
    $this->assertInstanceOf(HttpMethodsClientInterface::class, $this->subject->getHttpClient());
  }

  public function testRequestFactoryShouldBeRequestFactory(): void {
    $this->assertInstanceOf(RequestFactoryInterface::class, $this->subject->getRequestFactory());
  }

  public function testStreamFactoryShouldBeStreamFactory(): void {
    $this->assertInstanceOf(StreamFactoryInterface::class, $this->subject->getStreamFactory());
  }

  public function testUriFactoryShouldBeUriFactory(): void {
    $this->assertInstanceOf(UriFactoryInterface::class, $this->subject->getUriFactory());
  }
}
