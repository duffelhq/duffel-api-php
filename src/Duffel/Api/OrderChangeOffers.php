<?php

declare(strict_types=1);

namespace Duffel\Api;

class OrderChangeOffers extends AbstractApi {
  /**
   * @param string $orderChangeRequestId
   *
   * @return mixed
   */
  public function all(string $orderChangeRequestId = '') {
    if ('' === $orderChangeRequestId) {
      return $this->get('/air/order_change_offers');
    }

    return $this->get('/air/order_change_offers?order_change_request_id='.self::encodePath($orderChangeRequestId));
  }

  /**
   * @param string $id
   *
   * @return mixed
   */
  public function show(string $id) {
    return $this->get('/air/order_change_offers/'.self::encodePath($id));
  }
}
