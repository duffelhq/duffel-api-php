<?php

declare(strict_types=1);

namespace Duffel\Api;

class OfferRequests extends AbstractApi {
  public function all(array $parameters = []) {
    return $this->get('/air/offer_requests');
  }

  public function show(string $id) {
    return $this->get('/air/offer_requests/'.self::encodePath($id));
  }

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
