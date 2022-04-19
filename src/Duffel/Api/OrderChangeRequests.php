<?php

declare(strict_types=1);

namespace Duffel\Api;

class OrderChangeRequests extends AbstractApi {
  public function create(string $orderId, array $slices) {
    $params = array(
      "slices" => $slices,
      "order_id" => $orderId,
    );

    return $this->post('/air/order_change_requests', \array_filter($params, function ($value) {
      return null !== $value && (!\is_string($value) || '' !== $value);
    }));
  }

  public function show(string $id) {
    return $this->get('/air/order_change_requests/'.self::encodePath($id));
  }
}

