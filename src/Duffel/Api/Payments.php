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
    $resolver = $this->createOptionsResolver();
    $resolver->setRequired(['amount', 'currency', 'type']);

    $payment = $resolver->resolve($payment);

    $params = [
      'order_id' => $orderId,
      'payment' => $payment,
    ];

    return $this->post('/air/payments', $params);
  }
}
