<?php

declare(strict_types=1);

namespace Duffel\HttpClient;

use Duffel\HttpClient\ResponseParser;
use PHPUnit\Framework\TestCase;

class ResponseParserTest extends TestCase {
  public function testConstantContentTypeHeader(): void {
    $this->assertSame('Content-Type', ResponseParser::CONTENT_TYPE_HEADER);
  }

  public function testConstantJsonContentType(): void {
    $this->assertSame('application/json', ResponseParser::JSON_CONTENT_TYPE);
  }
}
