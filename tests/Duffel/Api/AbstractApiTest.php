<?php

declare(strict_types=1);

namespace Duffel\Tests\Api;

use Duffel\Api\AbstractApi;
use Duffel\Client;
use PHPUnit\Framework\TestCase;

class TestAbstractApi extends TestCase {
  public function testConstructorRequiresClient(): void {
    $client = new Client();
    $stub = $this->getMockForAbstractClass(AbstractApi::class, [$client]);

    $this->assertIsObject($stub);
  }
}
