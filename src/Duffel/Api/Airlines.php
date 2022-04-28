<?php

declare(strict_types=1);

namespace Duffel\Api;

class Airlines extends AbstractApi {
  public function all(array $parameters = []) {
    return $this->get('/air/airlines');
  }

  public function show(string $id) {
    return $this->get('/air/airlines/'.self::encodePath($id));
  }
}
