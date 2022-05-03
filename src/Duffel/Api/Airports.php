<?php

declare(strict_types=1);

namespace Duffel\Api;

class Airports extends AbstractApi {
  /**
   * @param array $parameters
   *
   * @return mixed
   */
  public function all(array $parameters = []) {
    return $this->get('/air/airports');
  }

  /**
   * @param string $id
   *
   * @return mixed
   */
  public function show(string $id) {
    return $this->get('/air/airports/'.self::encodePath($id));
  }
}
