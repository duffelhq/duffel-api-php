<?php

declare(strict_types=1);

namespace Duffel\HttpClient;

use Duffel\Exception\RuntimeException;

final class JsonArray {
  public static function decode(string $json): array
  {
    $data = \json_decode($json, true);

    if (\JSON_ERROR_NONE !== \json_last_error()) {
      throw new RuntimeException(\sprintf('json_decode error: %s', \json_last_error_msg()));
    }

    if (null === $data || !\is_array($data)) {
      throw new RuntimeException(\sprintf('json_decode error: Expected JSON of type array, %s given.', \get_debug_type($data)));
    }

    return $data;
  }

  public static function encode(array $value): string {
    $json = \json_encode($value, JSON_UNESCAPED_SLASHES);

    if (false === $json || \JSON_ERROR_NONE !== \json_last_error()) {
      throw new RuntimeException(\sprintf('json_encode error: %s', \json_last_error_msg()));
    }

    return $json;
  }
}
