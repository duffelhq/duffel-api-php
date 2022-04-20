<?php

declare(strict_types=1);

namespace Duffel\Api;

class Offers extends AbstractApi {
  public function all(string $offerRequestId) {
    return $this->get('/air/offers?offer_request_id='.self::encodePath($offerRequestId));
  }

  public function show(string $id, bool $returnAvailableServices = false) {
    if (true === $returnAvailableServices) {
      return $this->get('/air/offers/'.self::encodePath($id).'?return_available_services=true');
    }

    return $this->get('/air/offers/'.self::encodePath($id));
  }

  public function update(string $offer_id, string $offer_passenger_id, string $family_name, string $given_name, array $loyalty_programme_accounts) {
    $params = [
      'family_name' => $family_name,
      'given_name' => $given_name,
      'loyalty_programme_accounts' => $loyalty_programme_accounts,
    ];

    return $this->post('/air/offers/'.self::encodePath($offer_id).'/passengers/'.self::encodePath($offer_passenger_id), \array_filter($loyalty_programme_accounts, function ($value) {
      return null !== $value && (!\is_string($value) || '' !== $value);
    }));
  }
}
