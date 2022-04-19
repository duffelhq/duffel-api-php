<?php

declare(strict_types=1);

namespace Duffel\Api;

class SeatMaps extends AbstractApi {
  public function all(string $offerId) {
    return $this->get('/air/seat_maps?offer_id='.self::encodePath($offerId));
  }
}
