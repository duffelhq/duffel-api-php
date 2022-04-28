<?php

declare(strict_types=1);

namespace Duffel\Api;

use Duffel\Client;
use Duffel\HttpClient\ResponseParser;
use Duffel\HttpClient\JsonArray;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractApi {
  private $client;

  public function __construct(Client $client) {
    $this->client = $client;
  }

  protected function getAsResponse(string $uri, array $params = [], array $headers = []): ResponseInterface {
    return $this->client->getHttpClient()->get(self::prepareUri($uri, $params), $headers);
  }

  protected function get(string $uri, array $params = [], array $headers = []) {
    $response = $this->getAsResponse($uri, $params, $headers);

    return ResponseParser::getContent($response);
  }

  protected function post(string $uri, array $params = [], array $headers = []) {
    $body = self::prepareJsonBody($params);

    if (null !== $body) {
      $headers = self::addJsonContentType($headers);
    }

    $response = $this->client->getHttpClient()->post(self::prepareUri($uri), $headers, $body);

    return ResponseParser::getContent($response);
  }

  protected function put(string $uri, array $params = [], array $headers = []) {
    $body = self::prepareJsonBody($params);

    if (null !== $body) {
      $headers = self::addJsonContentType($headers);
    }

    $response = $this->client->getHttpClient()->put(self::prepareUri($uri), $headers, $body ?? '');

    return ResponseParser::getContent($response);
  }

  protected function delete(string $uri, array $params = [], array $headers = []) {
    $body = self::prepareJsonBody($params);

    if (null !== $body) {
      $headers = self::addJsonContentType($headers);
    }

    $response = $this->client->getHttpClient()->delete(self::prepareUri($uri), $headers, $body ?? '');

    return ResponseParser::getContent($response);
  }

  protected static function encodePath(string $uri): string
  {
    return \rawurlencode((string) $uri);
  }

  private static function prepareUri(string $uri, array $query = []): string {
    $query = \array_filter($query, function ($value): bool {
      return null !== $value;
    });

    return $uri;
  }

  private static function prepareJsonBody(array $params): ?string {
    $params = \array_filter($params, function ($value): bool {
      return null !== $value;
    });

    if (0 === \count($params)) {
      return null;
    }

    if (! array_key_exists("data", $params)) {
      $params = array("data" => $params);
    }

    return JsonArray::encode($params);
  }


  private static function addJsonContentType(array $headers): array {
    return \array_merge([ResponseParser::CONTENT_TYPE_HEADER => ResponseParser::JSON_CONTENT_TYPE], $headers);
  }
}
