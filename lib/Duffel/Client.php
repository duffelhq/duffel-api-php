<?php

namespace Duffel;

use Duffel\Api\OfferRequests;
use Duffel\Api\Offers;
use Duffel\Api\Orders;
use Duffel\Api\OrderCancellations;
use Duffel\Api\OrderChanges;
use Duffel\Api\OrderChangeOffers;
use Duffel\Api\OrderChangeRequests;
use Duffel\Api\SeatMaps;
use Duffel\Exception\AccessTokenMissingException;
use Duffel\HttpClient\Builder;
use Duffel\HttpClient\Plugin\ExceptionThrower;
use Http\Client\Common\HttpMethodsClientInterface;
use Http\Client\Common\Plugin\AddHostPlugin;
use Http\Client\Common\Plugin\AuthenticationPlugin;
use Http\Client\Common\Plugin\HeaderDefaultsPlugin;
use Http\Client\Common\Plugin\HistoryPlugin;
use Http\Client\Common\Plugin\RedirectPlugin;
use Http\Client\Common\Plugin\RetryPlugin;
use Http\Message\Authentication\Bearer;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

class Client {
  private const DEFAULT_API_URL = 'https://api.duffel.com/';
  private const DEFAULT_API_VERSION = 'beta';
  private const VERSION = '0.0.0-alpha';

  private $accessToken;
  private $apiUrl;
  private $apiVersion;
  private $httpClientBuilder;

  public function __construct(Builder $httpClientBuilder = null, string $accessToken = '', string $apiUrl = self::DEFAULT_API_URL, $apiVersion = self::DEFAULT_API_VERSION) {
    $this->httpClientBuilder = $builder = $httpClientBuilder ?? new Builder();
    $this->setAccessToken($accessToken);
    $this->apiUrl = $apiUrl;
    $this->apiVersion = $apiVersion;

    $builder->addPlugin(new AuthenticationPlugin(new Bearer($this->getAccessToken())));
    $builder->addPlugin(new ExceptionThrower());
    $builder->addPlugin(new HeaderDefaultsPlugin($this->getDefaultHeaders()));
    $builder->addPlugin(new RedirectPlugin());
    $builder->addPlugin(new RetryPlugin(array(
      "error_response_delay" => function ($request, $response, $retries): int {
        if ($response->getStatusCode() === 429) {
          if (null !== $response->getHeader("ratelimit-reset")) {
            // ratelimit-reset + 1_000_000 (microseconds, 1 second)
            $ratelimitSec = strtotime($ratelimitReset[0]);
            $nowSec = time();
            echo sprintf("Rate limited! Expires in %d seconds\n", ($ratelimitSec - $nowSec));
            return (($ratelimitSec - $nowSec) + 1) * 1000000;
          }
        }
      },
    )));

    $this->setUrl($apiUrl);
  }

  public function offerRequests(): OfferRequests {
    return new OfferRequests($this);
  }

  public function offers(): Offers {
    return new Offers($this);
  }

  public function orders(): Orders {
    return new Orders($this);
  }

  public function orderCancellations(): OrderCancellations {
    return new OrderCancellations($this);
  }

  public function orderChanges(): OrderChanges {
    return new OrderChanges($this);
  }

  public function orderChangeOffers(): OrderChangeOffers {
    return new OrderChangeOffers($this);
  }

  public function orderChangeRequests(): OrderChangeRequests {
    return new OrderChangeRequests($this);
  }

  public function seatMaps(): SeatMaps {
    return new SeatMaps($this);
  }

  public function setUrl(string $url): void {
    $uri = $this->getHttpClientBuilder()->getUriFactory()->createUri($url);

    $this->getHttpClientBuilder()->removePlugin(AddHostPlugin::class);
    $this->getHttpClientBuilder()->addPlugin(new AddHostPlugin($uri));
  }

  public function getHttpClient(): HttpMethodsClientInterface {
    return $this->getHttpClientBuilder()->getHttpClient();
  }

  protected function getHttpClientBuilder(): Builder {
    return $this->httpClientBuilder;
  }

  private function getAccessToken() {
    return $this->accessToken;
  }

  private function setAccessToken($token) {
    if (false !== getenv('DUFFEL_ACCESS_TOKEN')) {
      $this->accessToken = getenv('DUFFEL_ACCESS_TOKEN');
    } else if ('' !== trim($token) && strlen(trim($token)) > 0) {
      $this->accessToken = 'input! ' . $token;
    } else {
      throw new AccessTokenMissingException("You need to set a token");
    }

    $authentication = new Bearer('token');
  }

  /**
   * @return (mixed|string)[]
   *
   * @psalm-return array{'Duffel-Version': mixed, 'Content-Type': 'application/json', 'User-Agent': mixed}
   */
  private function getDefaultHeaders(): array {
    return array(
      "Duffel-Version" => $this->apiVersion,
      "Content-Type" => "application/json",
      "User-Agent" => $this->getUserAgent(),
    );
  }

  private function getUserAgent(): string {
    return "Duffel/" . $this->apiVersion . " " . "duffel_api_php/" . self::VERSION;
  }
}
