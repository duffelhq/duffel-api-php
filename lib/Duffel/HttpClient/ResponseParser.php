<?php

declare(strict_types=1);

namespace Duffel\HttpClient;

use Duffel\Exception\RuntimeException;
use Duffel\HttpClient\Util\JsonArray;
use Psr\Http\Message\ResponseInterface;

final class ResponseParser {
  public const CONTENT_TYPE_HEADER = 'Content-Type';
  public const JSON_CONTENT_TYPE = 'application/json';

  /**
   * @return array|string
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

    if (array_key_exists('data', $body)) {
      return $body['data'];
    }

    return $body;
  }

  public static function getPagination(ResponseInterface $response): array {
    $header = self::getHeader($response, 'Link');

    if (null === $header) {
      return [];
    }

    $pagination = [];
    foreach (\explode(',', $header) as $link) {
      \preg_match('/<(.*)>; rel="(.*)"/i', \trim($link, ','), $match);

      /** @var string[] $match */
      if (3 === \count($match)) {
        $pagination[$match[2]] = $match[1];
      }
    }

    return $pagination;
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

    if (isset($content['message'])) {
      $message = $content['message'];

      if (\is_string($message)) {
        return self::wrapWithRequestId($message, self::getHeader($response, 'x-request-id'));
      }

      if (\is_array($message)) {
        return self::wrapWithRequestId(self::getMessageAsString($content['message']), self::getHeader($response, 'x-request-id'));
      }
    }

    if (isset($content['error_description'])) {
      $error = $content['error_description'];

      if (\is_string($error)) {
        return self::wrapWithRequestId($error, self::getHeader($response, 'x-request-id'));
      }
    }

    if (isset($content['error'])) {
      $error = $content['error'];

      if (\is_string($error)) {
        return self::wrapWithRequestId($error, self::getHeader($response, 'x-request-id'));
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
        foreach ($messages as $error) {
          $errors[] = \sprintf($format, $field, $error);
        }
      } elseif (\is_int($field)) {
        $errors[] = $messages;
      } else {
        $errors[] = \sprintf($format, $field, $messages);
      }
    }

    return \implode(', ', $errors);
  }
}
