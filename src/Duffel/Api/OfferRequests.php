<?php

declare(strict_types=1);

namespace Duffel\Api;

class OfferRequests extends AbstractApi {
  /**
   * @param array $parameters
   *
   * @return mixed
   */
  public function all(array $parameters = []) {
    return $this->get('/air/offer_requests');
  }

  /**
   * @param string $id
   *
   * @return mixed
   */
  public function show(string $id) {
    return $this->get('/air/offer_requests/'.self::encodePath($id));
  }

  /**
   * @param string $cabin_class
   * @param array  $passengers
   * @param array  $slices
   *
   * @return mixed
   */
  public function create(string $cabin_class = "economy", array $passengers = array(), array $slices = array()) {
    $params = [
      'cabin_class' => $cabin_class,
      'passengers' => $passengers,
      'slices' => $slices,
    ];

    return $this->post('/air/offer_requests', \array_filter($params, function ($value) {
      return null !== $value && (!\is_string($value) || '' !== $value);
    }));
  }
}
