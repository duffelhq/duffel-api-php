<?php

declare(strict_types=1);

namespace Duffel\Api;

use Duffel\Client;
use Duffel\HttpClient\ResponseParser;
use Duffel\HttpClient\JsonArray;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractApi {
  /**
   *
   * @var Client
   */
  private $client;

  /**
   *
   * @param Client $client
   *
   * @return void
   */
  public function __construct(Client $client) {
    $this->client = $client;
  }

  /**
   * @return OptionsResolver
   */
  protected function createOptionsResolver(): OptionsResolver {
    $resolver = new OptionsResolver();

    return $resolver;
  }

  /**
   *
   * @param string               $uri
   * @param array                $params
   * @param array<string,string> $headers
   *
   * @throws \Http\Client\Exception
   *
   * @return \Psr\Http\Message\ResponseInterface
   */
  protected function getAsResponse(string $uri, array $params = [], array $headers = []): ResponseInterface {
    return $this->client->getHttpClient()->get(self::prepareUri($uri, $params), $headers);
  }

  /**
   * @param string               $uri
   * @param array<string,mixed>  $params
   * @param array<string,string> $headers
   *
   * @return mixed
   **/
  protected function get(string $uri, array $params = [], array $headers = []) {
    $response = $this->getAsResponse($uri, $params, $headers);

    return ResponseParser::getContent($response);
  }


  /**
   * @param string               $uri
   * @param array<string,mixed>  $params
   * @param array<string,string> $headers
   *
   * @return mixed
   **/
  protected function post(string $uri, array $params = [], array $headers = []) {
    $body = self::prepareJsonBody($params);

    if (null !== $body) {
      $headers = self::addJsonContentType($headers);
    }

    $response = $this->client->getHttpClient()->post(self::prepareUri($uri), $headers, $body);

    return ResponseParser::getContent($response);
  }


  /**
   * @param string               $uri
   * @param array<string,mixed>  $params
   * @param array<string,string> $headers
   *
   * @return mixed
   **/
  protected function put(string $uri, array $params = [], array $headers = []) {
    $body = self::prepareJsonBody($params);

    if (null !== $body) {
      $headers = self::addJsonContentType($headers);
    }

    $response = $this->client->getHttpClient()->put(self::prepareUri($uri), $headers, $body ?? '');

    return ResponseParser::getContent($response);
  }


  /**
   * @param string               $uri
   * @param array<string,mixed>  $params
   * @param array<string,string> $headers
   *
   * @return mixed
   **/
  protected function delete(string $uri, array $params = [], array $headers = []) {
    $body = self::prepareJsonBody($params);

    if (null !== $body) {
      $headers = self::addJsonContentType($headers);
    }

    $response = $this->client->getHttpClient()->delete(self::prepareUri($uri), $headers, $body ?? '');

    return ResponseParser::getContent($response);
  }


  /**
   * @param string $uri
   *
   * @return string
   **/
  protected static function encodePath(string $uri): string
  {
    return \rawurlencode($uri);
  }

  /**
   * @param string $uri
   * @param array  $query
   *
   * @return string
   **/
  private static function prepareUri(string $uri, array $query = []): string {
    $query = \array_filter($query, function ($value): bool {
      return null !== $value;
    });

    return $uri;
  }

  /**
   * @param array<string,mixed> $params
   *
   * @return string|null
   **/
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


  /**
   * @param array<string,string> $headers
   *
   * @return array<string,string>
   **/
  private static function addJsonContentType(array $headers): array {
    return \array_merge([ResponseParser::CONTENT_TYPE_HEADER => ResponseParser::JSON_CONTENT_TYPE], $headers);
  }
}
