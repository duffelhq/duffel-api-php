<?php

declare(strict_types=1);

namespace Duffel\Api;

class Refunds extends AbstractApi {
  /**
   * @param array $refund
   *
   * @return mixed
   */
  public function create(array $refund) {
    $params = [
      'amount' => $refund['amount'],
      'currency' => $refund['currency'],
      'payment_intent_id' => $refund['payment_intent_id'],
    ];

    return $this->post('/payments/refunds', \array_filter($params, function ($value) {
      return null !== $value && (!\is_string($value) || '' !== $value);
    }));
  }

  /**
   * @param string $id
   *
   * @return mixed
   */
  public function show(string $id) {
    return $this->get('/payments/refunds/'.self::encodePath($id));
  }
}
