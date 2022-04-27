<?php

declare(strict_types=1);

namespace Duffel\HttpClient;

use Duffel\HttpClient\JsonArray;
use PHPUnit\Framework\TestCase;

class JsonArrayTest extends TestCase {
  public function testDecodeWithInvalidJson(): void {
    $this->expectException(\RuntimeException::class);

    JsonArray::decode('["some", {"invalid": "data"]');
  }

  public function testDecodeWithEmptyJson(): void {
    $this->expectException(\RuntimeException::class);

    JsonArray::decode('');
  }

  public function testDecodeWithJsonString(): void {
    $this->expectException(\RuntimeException::class);

    JsonArray::decode('"some string"');
  }

  public function testDecodeWithJsonEmptyList(): void {
    $this->assertSame(JsonArray::decode('[""]'), [0 => '']);
  }

  public function testEncodeWithString(): void {
    $this->expectException(\TypeError::class);

    JsonArray::encode("some invalid data");
  }

  public function testEncodeWithInvalidJson(): void {
    $this->expectException(\RuntimeException::class);

    JsonArray::encode(["an invalid UTF-8 string" => "\xB1\x31"]);
  }
}