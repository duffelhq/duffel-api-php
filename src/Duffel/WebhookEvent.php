<?php

declare(strict_types=1);

namespace Duffel;

use Duffel\Exception\InvalidRequestSignatureException;

final class WebhookEvent {
  private const SIGNATURE_REGEXP = '/\At=(.+),v1=(.+)\z/';

  /**
   * @param string $requestBody
   * @param string $requestSignature
   * @param string $webhookSecret
   *
   * @return bool
   */
  public static function isGenuine(string $requestBody, string $requestSignature, string $webhookSecret): bool {
    try {
      $parsedSignature = self::parseSignature($requestSignature);
    } catch(InvalidRequestSignatureException $e) {
      /*
      If the signature doesn't even look like a valid one, then the webhook
      event can't be genuine
       */
      return false;
    }

    $calculatedHmac = self::calculateHmac(
      $webhookSecret,
      $requestBody,
      $parsedSignature['timestamp'],
    );

    return self::secureCompare($calculatedHmac, $parsedSignature['v1']);
  }

  /**
   * @param string $secret
   * @param string $payload
   * @param string $timestamp
   *
   * @return string
   */
  private static function calculateHmac(string $secret, string $payload, string $timestamp): string {
    $signedPayload = $timestamp.'.'.$payload;
    $hmacHash = hash_hmac('sha256', $signedPayload, $secret, true);
    $hmacHashHex = bin2hex($hmacHash);

    return strtolower( trim($hmacHashHex) );
  }

  /**
   * @param string $signature
   *
   * @return string[]
   *
   * @raise InvalidRequestSignatureError
   */
  private static function parseSignature(string $signature): array {
    $matches = [];

    if (preg_match(self::SIGNATURE_REGEXP, $signature, $matches)) {
      return [
        'v1' => $matches[2],
        'timestamp' => $matches[1],
      ];
    }

    throw new InvalidRequestSignatureException();
  }

  /**
   * @param string $a
   * @param string $b
   *
   * @return bool
   */
  private static function secureCompare(string $a, string $b): bool {
    if (mb_strlen($a) !== mb_strlen($b)) {
      return false;
    }

    $l = unpack("C*", $a);

    $r = 0;
    $i = 0;

    foreach(str_split($b) as $v) {
      $o = mb_ord($v);

      $r |= $o ^ $l[$i += 1];
    }

    return 0 === $r;
  }
}
