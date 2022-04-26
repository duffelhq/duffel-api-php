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

class BuilderTest extends TestCase {
  private $subject;

  public function setUp(): void {
    $this->subject = new Builder(
      $this->createMock(ClientInterface::class),
      $this->createMock(RequestFactoryInterface::class),
      $this->createMock(StreamFactoryInterface::class)
    );
  }

  public function testAddPluginShouldInvalidateHttpClient(): void {
    $client = $this->subject->getHttpClient();

    $this->subject->addPlugin($this->createMock(Plugin::class));

    $this->assertNotSame($client, $this->subject->getHttpClient());
  }
}
