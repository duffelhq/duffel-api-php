<?php

declare(strict_types=1);

namespace Duffel\HttpClient;

use Duffel\HttpClient\ResponseParser;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class ResponseParserTest extends TestCase {
  private $stub;

  public function setUp(): void {
    $this->stub = $this->createStub(ResponseInterface::class);
  }

  public function testConstantContainsExpectedContentTypeHeader(): void {
    $this->assertSame('Content-Type', ResponseParser::CONTENT_TYPE_HEADER);
  }

  public function testConstantContainsExpectedJsonContentType(): void {
    $this->assertSame('application/json', ResponseParser::JSON_CONTENT_TYPE);
  }

  public function testGetContentAsJsonWithoutDataKey(): void {
    $this->stub->method('getBody')
               ->willReturn('{"some": {"keys": ["with", "values"]} }');
    $this->stub->method('getHeaderLine')
               ->with('Content-Type')
               ->willReturn('application/json');

    $this->assertSame(["some" => ["keys" => ["with", "values"]]], ResponseParser::getContent($this->stub));
  }

  public function testGetContentAsJsonWithDataKey(): void {
    $this->stub->method('getBody')
               ->willReturn('{"data": {"some": {"keys": ["with", "values"]} } }');
    $this->stub->method('getHeaderLine')
               ->with('Content-Type')
               ->willReturn('application/json');

    $this->assertSame(["some" => ["keys" => ["with", "values"]]], ResponseParser::getContent($this->stub));
  }

  public function testGetErrorMessage(): void {
    $this->stub->method('getBody')
               ->willReturn('{"message": "some error message"}');
    $this->stub->method('getHeaderLine')
               ->with('Content-Type')
               ->willReturn('application/json');
    $this->stub->method('getHeader')
               ->with('x-request-id')
               ->willReturn(['some-request-id']);

    $this->assertSame('[some-request-id]: some error message', ResponseParser::getErrorMessage($this->stub));
  }
}
