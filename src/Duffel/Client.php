<?php

declare(strict_types=1);

namespace Duffel;

use Duffel\Api\Aircraft;
use Duffel\Api\Airlines;
use Duffel\Api\Airports;
use Duffel\Api\OfferRequests;
use Duffel\Api\Offers;
use Duffel\Api\OrderCancellations;
use Duffel\Api\OrderChangeOffers;
use Duffel\Api\OrderChangeRequests;
use Duffel\Api\OrderChanges;
use Duffel\Api\Orders;
use Duffel\Api\PaymentIntents;
use Duffel\Api\Payments;
use Duffel\Api\Refunds;
use Duffel\Api\SeatMaps;
use Duffel\Api\Webhooks;
use Duffel\Exception\InvalidAccessTokenException;
use Duffel\HttpClient\Builder;
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
  private const DEFAULT_API_VERSION = 'v1';
  private const VERSION = '0.0.0-alpha';

  /**
   * @var string
   */
  private $accessToken;

  /**
   * @var string
   */
  private $apiUrl;

  /**
   * @var string
   */
  private $apiVersion;

  /**
   * @var Builder
   */
  private $httpClientBuilder;

  public function __construct(Builder $httpClientBuilder = null) {
    $this->httpClientBuilder = $builder = $httpClientBuilder ?? new Builder();
    $this->accessToken = '';
    $this->apiUrl = self::DEFAULT_API_URL;
    $this->apiVersion = self::DEFAULT_API_VERSION;

    $builder->addPlugin(new HeaderDefaultsPlugin($this->getDefaultHeaders()));

    $this->setUrl($this->apiUrl);
  }

  public function aircraft(): Aircraft {
    return new Aircraft($this);
  }

  public function airlines(): Airlines {
    return new Airlines($this);
  }

  public function airports(): Airports {
    return new Airports($this);
  }

  public function offerRequests(): OfferRequests {
    return new OfferRequests($this);
  }

  public function offers(): Offers {
    return new Offers($this);
  }

  public function orderCancellations(): OrderCancellations {
    return new OrderCancellations($this);
  }

  public function orderChangeOffers(): OrderChangeOffers {
    return new OrderChangeOffers($this);
  }

  public function orderChangeRequests(): OrderChangeRequests {
    return new OrderChangeRequests($this);
  }

  public function orderChanges(): OrderChanges {
    return new OrderChanges($this);
  }

  public function orders(): Orders {
    return new Orders($this);
  }

  public function paymentIntents(): PaymentIntents {
    return new PaymentIntents($this);
  }

  public function payments(): Payments {
    return new Payments($this);
  }

  public function refunds(): Refunds {
    return new Refunds($this);
  }

  public function seatMaps(): SeatMaps {
    return new SeatMaps($this);
  }

  public function webhooks(): Webhooks {
    return new Webhooks($this);
  }

  public function getAccessToken(): ?string {
    return $this->accessToken;
  }

  public function setAccessToken(string $token): void {
    if ('' === trim($token)) {
      throw new InvalidAccessTokenException("You need to set a token");
    }

    $this->accessToken = trim($token);
    $this->httpClientBuilder->addPlugin(new AuthenticationPlugin(new Bearer($this->accessToken)));
  }

  public function getApiVersion(): string {
    return $this->apiVersion;
  }

  public function setApiVersion(string $apiVersion): void {
    $this->apiVersion = $apiVersion;
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
