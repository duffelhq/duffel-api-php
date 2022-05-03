<?php

declare(strict_types=1);

namespace Duffel\Api;

class Aircraft extends AbstractApi {
  /**
   * @param array $parameters
   *
   * @return mixed
   */
  public function all(array $parameters = []) {
    return $this->get('/air/aircraft');
  }

  /**
   * @param string $id
   *
   * @return mixed
   */
  public function show(string $id) {
    return $this->get('/air/aircraft/'.self::encodePath($id));
  }
}
