<?php

declare(strict_types=1);

namespace Duffel\HttpClient;

use Duffel\Exception\RuntimeException;
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

  public function testGetContentWithNilBodyAndContentTypeAsJson(): void {
    $this->stub->method('getBody')
               ->willReturn('');
    $this->stub->method('getHeaderLine')
               ->with('Content-Type')
               ->willReturn('application/json');

    $this->assertSame('', ResponseParser::getContent($this->stub));
  }

  public function testGetContentWithNullBodyAndContentTypeAsJson(): void {
    $this->stub->method('getBody')
               ->willReturn('null');
    $this->stub->method('getHeaderLine')
               ->with('Content-Type')
               ->willReturn('application/json');

    $this->assertSame('null', ResponseParser::getContent($this->stub));
  }

  public function testGetContentWithTrueBodyAndContentTypeAsJson(): void {
    $this->stub->method('getBody')
               ->willReturn('true');
    $this->stub->method('getHeaderLine')
               ->with('Content-Type')
               ->willReturn('application/json');

    $this->assertSame('true', ResponseParser::getContent($this->stub));
  }

  public function testGetContentWithFalseBodyAndContentTypeAsJson(): void {
    $this->stub->method('getBody')
               ->willReturn('false');
    $this->stub->method('getHeaderLine')
               ->with('Content-Type')
               ->willReturn('application/json');

    $this->assertSame('false', ResponseParser::getContent($this->stub));
  }

  public function testGetErrorMessageTransformsList(): void {
    $this->stub->method('getBody')
               ->willReturn('{
               "errors": [
  {
    "code": "missing_authorization_header",
      "documentation_url": "https://duffel.com/docs/api/overview/errors",
      "message": "The \'Authorization\' header needs to be set and contain a valid API token.",
      "title": "Missing authorization header",
      "type": "authentication_error"
  }
  ],
    "meta": {
    "request_id": "FZW0H3HdJwKk5HMAAKxB",
      "status": 401
  }
  }');
    $this->stub->method('getHeaderLine')
               ->with('Content-Type')
               ->willReturn('application/json');
    $this->stub->method('getHeader')
               ->with('x-request-id')
               ->willReturn(['some-request-id']);

    $this->assertSame(
      '[some-request-id]: code: missing_authorization_header, documentation_url: https://duffel.com/docs/api/overview/errors, message: The \'Authorization\' header needs to be set and contain a valid API token., title: Missing authorization header, type: authentication_error',
      ResponseParser::getErrorMessage($this->stub)
    );
  }

  public function testGetErrorMessageWhenRequestIdIsMissingReturnsUnwrappedMessage(): void {
    $this->stub->method('getBody')
               ->willReturn('{
               "errors": [
  {
    "code": "missing_authorization_header",
      "documentation_url": "https://duffel.com/docs/api/overview/errors",
      "message": "The \'Authorization\' header needs to be set and contain a valid API token.",
      "title": "Missing authorization header",
      "type": "authentication_error"
  }
  ],
    "meta": {
    "request_id": "FZW0H3HdJwKk5HMAAKxB",
      "status": 401
  }
  }');
    $this->stub->method('getHeaderLine')
               ->with('Content-Type')
               ->willReturn('application/json');
    $this->stub->method('getHeader')
               ->with('x-request-id')
               ->willReturn([]);

    $this->assertSame(
      'code: missing_authorization_header, documentation_url: https://duffel.com/docs/api/overview/errors, message: The \'Authorization\' header needs to be set and contain a valid API token., title: Missing authorization header, type: authentication_error',
      ResponseParser::getErrorMessage($this->stub)
    );
  }

  public function testGetErrorMessageWhenJsonDecodeFailsReturnsNull(): void {
    $this->stub->method('getBody')
               ->willThrowException(new RuntimeException());
    $this->stub->method('getHeaderLine')
               ->with('Content-Type')
               ->willReturn('application/json');

    $this->assertSame(null, ResponseParser::getErrorMessage($this->stub));
  }

  public function testGetErrorMessageWhenJsonIsStringReturnsNull(): void {
    $this->stub->method('getBody')
               ->willReturn('"some error string in JSON format"');
    $this->stub->method('getHeaderLine')
               ->with('Content-Type')
               ->willReturn('application/json');

    $this->assertSame(null, ResponseParser::getErrorMessage($this->stub));
  }

  public function testGetErrorMessageWhenTextIsStringReturnsNull(): void {
    $this->stub->method('getBody')
               ->willReturn('some error string in text format');
    $this->stub->method('getHeaderLine')
               ->with('Content-Type')
               ->willReturn('text/plain');

    $this->assertSame(null, ResponseParser::getErrorMessage($this->stub));
  }

  public function testGetErrorMessageWhenJsonIsObjectWithoutErrorsKeyReturnsNull(): void {
    $this->stub->method('getBody')
               ->willReturn('{"some": ["error", "string in JSON", "format"]}');
    $this->stub->method('getHeaderLine')
               ->with('Content-Type')
               ->willReturn('application/json');

    $this->assertSame(null, ResponseParser::getErrorMessage($this->stub));
  }
}
