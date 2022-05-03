<?php

declare(strict_types=1);

namespace Duffel\HttpClient;

use Duffel\Exception\RuntimeException;
use Duffel\HttpClient\JsonArray;
use Psr\Http\Message\ResponseInterface;

final class ResponseParser {
  public const CONTENT_TYPE_HEADER = 'Content-Type';
  public const JSON_CONTENT_TYPE = 'application/json';

  /**
   * @param ResponseInterface $response
   *
   * @return mixed|string
   */
  public static function getContent(ResponseInterface $response) {
    $body = (string) $response->getBody();

    if (!\in_array($body, ['', 'null', 'true', 'false'], true) && 0 === \strpos($response->getHeaderLine(self::CONTENT_TYPE_HEADER), self::JSON_CONTENT_TYPE)) {
      $decoded = JsonArray::decode($body);

      if (array_key_exists('data', $decoded)) {
        return $decoded['data'];
      }

      return $decoded;
    }

    return $body;
  }

  private static function getHeader(ResponseInterface $response, string $name): ?string {
    $headers = $response->getHeader($name);

    return \array_shift($headers);
  }

  public static function getErrorMessage(ResponseInterface $response): ?string {
    try {
      $content = self::getContent($response);
    } catch (RuntimeException $e) {
      return null;
    }

    if (!\is_array($content)) {
      return null;
    }

    if (isset($content['errors'])) {
      $errors = $content['errors'];

      if (\is_array($errors)) {
        $requestId = self::getHeader($response, 'x-request-id');

        if (\is_null($requestId)) {
          return self::getMessageAsString($errors);
        }

        return self::wrapWithRequestId(self::getMessageAsString($errors), $requestId);
      }
    }

    return null;
  }

  private static function wrapWithRequestId(string $message, string $requestId): string {
    $format = '[%s]: %s';

    return \sprintf($format, $requestId, $message);
  }

  private static function getMessageAsString(array $message): string {
    $format = '"%s" %s';
    $errors = [];

    foreach ($message as $field => $messages) {
      if (\is_array($messages)) {
        $messages = \array_unique($messages);
        foreach ($messages as $error_key => $error_value) {
          $errors[] = \sprintf('%s: %s', $error_key, $error_value);
        }
      }
    }

    return \implode(', ', $errors);
  }
}
