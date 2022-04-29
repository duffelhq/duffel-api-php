<?php

declare(strict_types=1);

namespace Duffel\Api;

class OrderChanges extends AbstractApi {
  public function create(string $orderChangeOfferId) {
    $params = array(
      "selected_order_change_offer" => $orderChangeOfferId,
    );

    return $this->post('/air/order_changes', \array_filter($params, function ($value) {
      return null !== $value && (!\is_string($value) || '' !== $value);
    }));
  }

  public function confirm(string $id, array $payment) {
    $params = array(
      "payment" => $payment,
    );

    return $this->post('/air/order_changes/'.self::encodePath($id).'/actions/confirm', \array_filter($params, function ($value) {
      return null !== $value && (!\is_string($value) || '' !== $value);
    }));
  }

  public function show(string $id) {
    return $this->get('/air/order_changes/'.self::encodePath($id));
  }
}
