<?php

declare(strict_types=1);

namespace Duffel\Api;

class OrderCancellations extends AbstractApi {
  public function all() {
    return $this->get('/air/order_cancellations');
  }

  public function show(string $id) {
    return $this->get('/air/order_cancellations/'.self::encodePath($id));
  }

  public function create(string $orderId) {
    $params = array(
      "order_id" => $orderId
    );

    return $this->post('/air/order_cancellations', \array_filter($params, function ($value) {
      return null !== $value && (!\is_string($value) || '' !== $value);
    }));
  }

  public function confirm(string $id) {
    return $this->post('/air/order_cancellations/'.self::encodePath($id).'/actions/confirm');
  }
}

