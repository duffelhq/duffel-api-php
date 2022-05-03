<?php

declare(strict_types=1);

namespace Duffel\Api;

class Payments extends AbstractApi {
  /**
   * @param string $orderId
   * @param array  $payment
   *
   * @return mixed
   */
  public function create(string $orderId, array $payment) {
    $params = [
      'order_id' => $orderId,
      'payment' => [
        'amount' => $payment['amount'],
        'currency' => $payment['currency'],
        'type' => $payment['type'],
      ],
    ];

    return $this->post('/air/payments', \array_filter($params, function ($value) {
      return null !== $value && (!\is_string($value) || '' !== $value);
    }));
  }
}
