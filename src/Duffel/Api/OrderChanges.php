<?php

declare(strict_types=1);

namespace Duffel\Api;

class OrderChanges extends AbstractApi {
  /**
   * @param string $orderChangeOfferId
   *
   * @return mixed
   */
  public function create(string $orderChangeOfferId) {
    $params = array(
      "selected_order_change_offer" => $orderChangeOfferId,
    );

    return $this->post('/air/order_changes', \array_filter($params, function ($value) {
      return null !== $value && (!\is_string($value) || '' !== $value);
    }));
  }

  /**
   * @param string $id
   * @param array  $payment
   *
   * @return mixed
   */
  public function confirm(string $id, array $payment) {
    $params = array(
      "payment" => $payment,
    );

    return $this->post('/air/order_changes/'.self::encodePath($id).'/actions/confirm', \array_filter($params, function ($value) {
      return null !== $value && (!\is_string($value) || '' !== $value);
    }));
  }

  /**
   * @param string $id
   *
   * @return mixed
   */
  public function show(string $id) {
    return $this->get('/air/order_changes/'.self::encodePath($id));
  }
}
