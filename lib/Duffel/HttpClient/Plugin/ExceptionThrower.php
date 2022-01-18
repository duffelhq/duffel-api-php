<?php

declare(strict_types=1);

namespace Duffel\HttpClient\Plugin;

use Duffel\Exception\RateLimitErrorException;
use Duffel\Exception\ErrorException;
use Duffel\Exception\ExceptionInterface;
use Duffel\Exception\RuntimeException;
use Duffel\Exception\ValidationErrorException;
use Duffel\HttpClient\ResponseParser;
use Http\Client\Common\Plugin;
use Http\Promise\Promise;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class ExceptionThrower implements Plugin {
  public function handleRequest(RequestInterface $request, callable $next, callable $first): Promise {
    return $next($request)->then(function (ResponseInterface $response): ResponseInterface {
      $status = $response->getStatusCode();

      if ($status >= 400 && $status < 600) {
        throw self::createException($status, ResponseParser::getErrorMessage($response) ?? $response->getReasonPhrase());
      }

      return $response;
    });
  }

  private static function createException(int $status, string $message): ExceptionInterface {
    if (400 === $status || 422 === $status) {
      return new ValidationErrorException($message, $status);
    }

    if (429 === $status) {
      return new RateLimitErrorException($message, $status);
    }

    return new RuntimeException($message, $status);
  }
}
