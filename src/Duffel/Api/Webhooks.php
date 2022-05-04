<?php

declare(strict_types=1);

namespace Duffel\Api;

class Webhooks extends AbstractApi {
  /**
   * @param string $url
   * @param array  $events
   *
   * @return mixed
   */
  public function create(string $url, array $events) {
    $params = [
      'events' => $events,
      'url' => $url,
    ];

    return $this->post('/air/webhooks', $params);
  }

  /**
   * @param string $id
   *
   * @return mixed
   */
  public function ping(string $id) {
    return $this->post('/air/webhooks/'.self::encodePath($id).'/actions/ping');
  }

  /**
   * @param string $id
   * @param bool   $active
   *
   * @return mixed
   */
  public function update(string $id, bool $active) {
    $params = [
      'active' => $active,
    ];

    return $this->post('/air/webhooks/'.self::encodePath($id), $params);
  }
}
