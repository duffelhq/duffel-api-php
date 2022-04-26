<?php

declare(strict_types=1);

namespace Duffel\HttpClient;

use Http\Client\Common\HttpMethodsClient;
use Http\Client\Common\HttpMethodsClientInterface;
use Http\Client\Common\Plugin;
use Http\Client\Common\Plugin\Cache\Generator\HeaderCacheKeyGenerator;
use Http\Client\Common\Plugin\CachePlugin;
use Http\Client\Common\PluginClientFactory;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

final class Builder {
  private $httpClient;
  private $requestFactory;
  private $streamFactory;
  private $uriFactory;
  private $cachePlugin;
  private $plugins = [];
  private $pluginClient;

  public function __construct(
    ClientInterface $httpClient = null,
    RequestFactoryInterface $requestFactory = null,
    StreamFactoryInterface $streamFactory = null,
    UriFactoryInterface $uriFactory = null
  ) {
    $this->httpClient = $httpClient ?? Psr18ClientDiscovery::find();
    $this->requestFactory = $requestFactory ?? Psr17FactoryDiscovery::findRequestFactory();
    $this->streamFactory = $streamFactory ?? Psr17FactoryDiscovery::findStreamFactory();
    $this->uriFactory = $uriFactory ?? Psr17FactoryDiscovery::findUriFactory();
  }

  public function getHttpClient(): HttpMethodsClientInterface {
    if (null === $this->pluginClient) {
      $plugins = $this->plugins;

      $this->pluginClient = new HttpMethodsClient(
        (new PluginClientFactory())->createClient($this->httpClient, $plugins),
        $this->requestFactory,
        $this->streamFactory
      );
    }

    return $this->pluginClient;
  }

  public function getRequestFactory(): RequestFactoryInterface {
    return $this->requestFactory;
  }

  public function getStreamFactory(): StreamFactoryInterface {
    return $this->streamFactory;
  }

  public function getUriFactory(): UriFactoryInterface {
    return $this->uriFactory;
  }

  public function addPlugin(Plugin $plugin): void {
    $this->plugins[] = $plugin;
    $this->pluginClient = null;
  }

  public function removePlugin(string $fqcn): void
  {
    foreach ($this->plugins as $idx => $plugin) {
      if ($plugin instanceof $fqcn) {
        unset($this->plugins[$idx]);
        $this->pluginClient = null;
      }
    }
  }
}
