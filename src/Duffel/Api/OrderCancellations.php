<?php

declare(strict_types=1);

namespace Duffel\Api;

class OrderCancellations extends AbstractApi {
  /**
   * @return mixed
   */
  public function all() {
    return $this->get('/air/order_cancellations');
  }

  /**
   * @param string $id
   *
   * @return mixed
   */
  public function show(string $id) {
    return $this->get('/air/order_cancellations/'.self::encodePath($id));
  }


  /**
   * @param string $orderId
   *
   * @return mixed
   */
  public function create(string $orderId) {
    $params = array(
      "order_id" => $orderId
    );

    return $this->post('/air/order_cancellations', $params);
  }


  /**
   * @param string $id
   *
   * @return mixed
   */
  public function confirm(string $id) {
    return $this->post('/air/order_cancellations/'.self::encodePath($id).'/actions/confirm');
  }
}
