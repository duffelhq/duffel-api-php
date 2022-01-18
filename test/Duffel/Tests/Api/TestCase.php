<?php

declare(strict_types=1);

namespace Duffel\Tests\Api;

use Duffel\Client;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Psr\Http\Client\ClientInterface;

abstract class TestCase extends BaseTestCase {
  abstract protected function getApiClass();

  protected function getApiMock(array $methods = []) {
    $httpClient = $this->getMockBuilder(ClientInterface::class)
                       ->setMethods(['sendRequest'])
                       ->getMock();
    $httpClient
      ->expects($this->any())
      ->method('sendRequest');

    $client = Client::createWithHttpClient($httpClient);

    return $this->getMockBuilder($this->getApiClass())
                ->setMethods(\array_merge(['getAsResponse', 'get', 'post', 'postRaw', 'patch', 'delete', 'put', 'head'], $methods))
                ->setConstructorArgs([$client, null])
                ->getMock();
  }
}
