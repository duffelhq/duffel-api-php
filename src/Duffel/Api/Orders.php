<?php

declare(strict_types=1);

namespace Duffel\Api;

class Orders extends AbstractApi {
  /**
   * @return mixed
   */
  public function all() {
    return $this->get('/air/orders');
  }

  /**
   * @param string $id
   *
   * @return mixed
   */
  public function show(string $id) {
    return $this->get('/air/orders/'.self::encodePath($id));
  }

  /**
   * @param array $params
   *
   * @return mixed
   */
  public function create(array $params) {
    return $this->post('/air/orders', \array_filter($params, function ($value) {
      return null !== $value && (!\is_string($value) || '' !== $value);
    }));
  }
}
