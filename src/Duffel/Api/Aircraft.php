<?php

declare(strict_types=1);

namespace Duffel\Api;

class Aircraft extends AbstractApi {
  public function all(array $parameters = []) {
    return $this->get('/air/aircraft');
  }

  public function show(string $id) {
    return $this->get('/air/aircraft/'.self::encodePath($id));
  }
}
