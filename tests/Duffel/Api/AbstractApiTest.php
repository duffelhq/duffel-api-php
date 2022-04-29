<?php

declare(strict_types=1);

namespace Duffel\Tests\Api;

use Duffel\Api\AbstractApi;
use Duffel\Client;
use Duffel\HttpClient\Builder;
use Http\Mock\Client as MockClient;
use PHPUnit\Framework\TestCase;

class AbstractApiTest extends TestCase {
  private $builder;
  private $client;
  private $mock;

  public function setUp(): void {
    $this->mock = new MockClient();
    $this->builder = new Builder($this->mock);
    $this->client = new Client($this->builder);
  }

  public function testConstructorRequiresClient(): void {
    $stub = $this->getMockForAbstractClass(AbstractApi::class, [$this->client]);

    $this->assertIsObject($stub);
  }

  public function testGetAsResponseCallsHttpClientMethod(): void {
    $this->subject = new class($this->client) extends AbstractApi {
      public function testGet(string $uri, array $params = [], array $headers = []) {
        return $this->get($uri, $params, $headers);
      }
    };

    $this->subject->testGet('some-get-uri');

    $requests = $this->mock->getRequests();
    $this->assertEquals(1, count($requests));

    $request = array_shift($requests);
    $this->assertEquals('GET', $request->getMethod());
    $this->assertEquals('/some-get-uri', $request->getUri()->getPath());
    $this->assertEquals('', $request->getUri()->getQuery());
    $this->assertEquals(null, $request->getBody()->getSize());
    $this->assertContains('application/json', $request->getHeader('Content-Type'));
  }

  public function testGetAsResponseWithQueryParametersCallsHttpClientMethod(): void {
    $this->subject = new class($this->client) extends AbstractApi {
      public function testGet(string $uri, array $params = [], array $headers = []) {
        return $this->get($uri, $params, $headers);
      }
    };

    $this->subject->testGet('some-get-uri', ['some', 'query', 'params']);

    $requests = $this->mock->getRequests();
    $this->assertEquals(1, count($requests));

    $request = array_shift($requests);
    $this->assertEquals('GET', $request->getMethod());
    $this->assertEquals('/some-get-uri', $request->getUri()->getPath());
    $this->assertEquals('', $request->getUri()->getQuery());
    $this->assertEquals(null, $request->getBody()->getSize());
    $this->assertContains('application/json', $request->getHeader('Content-Type'));
  }

  public function testPostAsResponseCallsHttpClientMethod(): void {
    $this->subject = new class($this->client) extends AbstractApi {
      public function testPost(string $uri, array $params = [], array $headers = []) {
        return $this->post($uri, $params, $headers);
      }
    };

    $this->subject->testPost('some-post-uri', ['some', 'post', 'data']);

    $requests = $this->mock->getRequests();
    $this->assertEquals(1, count($requests));

    $request = array_shift($requests);
    $this->assertEquals('POST', $request->getMethod());
    $this->assertEquals('/some-post-uri', $request->getUri()->getPath());
    $this->assertEquals('{"data":["some","post","data"]}', $request->getBody()->__toString());
    $this->assertContains('application/json', $request->getHeader('Content-Type'));
  }

  public function testPostAsResponseWithNullDataCallsHttpClientMethod(): void {
    $this->subject = new class($this->client) extends AbstractApi {
      public function testPost(string $uri, array $params = [], array $headers = []) {
        return $this->post($uri, $params, $headers);
      }
    };

    $this->subject->testPost('some-post-uri');

    $requests = $this->mock->getRequests();
    $this->assertEquals(1, count($requests));

    $request = array_shift($requests);
    $this->assertEquals('POST', $request->getMethod());
    $this->assertEquals('/some-post-uri', $request->getUri()->getPath());
    $this->assertEquals(null, $request->getBody()->getSize());
    $this->assertContains('application/json', $request->getHeader('Content-Type'));
  }

  public function testPutAsResponseCallsHttpClientMethod(): void {
    $this->subject = new class($this->client) extends AbstractApi {
      public function testPut(string $uri, array $params = [], array $headers = []) {
        return $this->put($uri, $params, $headers);
      }
    };

    $this->subject->testPut('some-put-uri', ['some', 'put', 'data']);

    $requests = $this->mock->getRequests();
    $this->assertEquals(1, count($requests));

    $request = array_shift($requests);
    $this->assertEquals('PUT', $request->getMethod());
    $this->assertEquals('/some-put-uri', $request->getUri()->getPath());
    $this->assertEquals('{"data":["some","put","data"]}', $request->getBody()->__toString());
    $this->assertContains('application/json', $request->getHeader('Content-Type'));
  }

  public function testDeleteAsResponseCallsHttpClientMethod(): void {
    $this->subject = new class($this->client) extends AbstractApi {
      public function testDelete(string $uri, array $params = [], array $headers = []) {
        return $this->delete($uri, $params, $headers);
      }
    };

    $this->subject->testDelete('some-delete-uri', ['some', 'delete', 'data']);

    $requests = $this->mock->getRequests();
    $this->assertEquals(1, count($requests));

    $request = array_shift($requests);
    $this->assertEquals('DELETE', $request->getMethod());
    $this->assertEquals('/some-delete-uri', $request->getUri()->getPath());
    $this->assertEquals('{"data":["some","delete","data"]}', $request->getBody()->__toString());
    $this->assertContains('application/json', $request->getHeader('Content-Type'));
  }
}
