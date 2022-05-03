<?php

declare(strict_types=1);

namespace Duffel\Api;

class PaymentIntents extends AbstractApi {
  /**
   * @param array $payment
   *
   * @return mixed
   */
  public function create(array $payment) {
    $params = [
      'amount' => $payment['amount'],
      'currency' => $payment['currency'],
    ];

    return $this->post('/payments/payment_intents', \array_filter($params, function ($value) {
      return null !== $value && (!\is_string($value) || '' !== $value);
    }));
  }

  /**
   * @param string $id
   *
   * @return mixed
   */
  public function confirm(string $id) {
    return $this->post('/payments/payment_intents/'.self::encodePath($id).'/actions/confirm');
  }

  /**
   * @param string $id
   *
   * @return mixed
   */
  public function show(string $id) {
    return $this->get('/payments/payment_intents/'.self::encodePath($id));
  }
}
