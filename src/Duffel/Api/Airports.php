<?php

declare(strict_types=1);

namespace Duffel\Api;

class Airports extends AbstractApi {
  public function all(array $parameters = []) {
    return $this->get('/air/airports');
  }

  public function show(string $id) {
    return $this->get('/air/airports/'.self::encodePath($id));
  }
}
