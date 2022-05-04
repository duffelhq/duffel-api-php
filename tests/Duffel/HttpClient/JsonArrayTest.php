<?php

declare(strict_types=1);

namespace Duffel\HttpClient;

use Duffel\HttpClient\JsonArray;
use PHPUnit\Framework\TestCase;

class JsonArrayTest extends TestCase {
  public function testDecodeWithJsonEmptyList(): void {
    $this->assertSame([0 => ''], JsonArray::decode('[""]'));
  }

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

  public function testEncodeWithArray(): void {
    $this->assertSame('["some","valid","data"]', JsonArray::encode(['some', 'valid', 'data']));
  }

  public function testEncodeWithArrayOfArrays(): void {
    $this->assertSame('{"some":["valid","data"]}', JsonArray::encode(['some' => ['valid', 'data']]));
  }

  public function testEncodeWithObjectOfArrayOfUrls(): void {
    $this->assertSame('{"urls":["https://valid.dom","http://another-valid.tld/path"]}', JsonArray::encode(['urls' => ['https://valid.dom', 'http://another-valid.tld/path']]));
  }

  public function testEncodeWithString(): void {
    $this->expectException(\TypeError::class);

    JsonArray::encode("some invalid data");
  }

  public function testEncodeWithObjectAndUrl(): void {
    $this->assertSame('{"url":"https://some-valid-url.dom/path"}', JsonArray::encode(['url' => 'https://some-valid-url.dom/path']));
  }

  public function testEncodeWithInvalidJson(): void {
    $this->expectException(\RuntimeException::class);

    JsonArray::encode(["an invalid UTF-8 string" => "\xB1\x31"]);
  }
}
