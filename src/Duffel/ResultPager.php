<?php

declare(strict_types=1);

namespace Duffel;

use Closure;
use Generator;
use Duffel\Api\AbstractApi;
use Duffel\Exception\RuntimeException;
use Duffel\HttpClient\ResponseParser;
use ValueError;

final class ResultPager implements ResultPagerInterface {
    /**
     * @var int
     */
    private const DEFAULT_LIMIT = 50;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var array<string,string>
     */
    private $pagination;

    /**
     * @param Client   $client
     * @param int|null $limit
     *
     * @return void
     */
    public function __construct(Client $client, int $limit = null) {
        if (null !== $limit && ($limit < 1 || $limit > 200)) {
            throw new ValueError(\sprintf('%s::__construct(): Argument #2 ($limit) must be between 1 and 200, or null', self::class));
        }

        $this->client 	  = $client;
        $this->limit 	  = $limit ?? self::DEFAULT_LIMIT;
        $this->pagination = [];
    }

    /**
     * @param AbstractApi $api
     * @param string      $method
     * @param array       $parameters
     *
     * @throws \Http\Client\Exception
     *
     * @return array
     */
    public function fetch(AbstractApi $api, string $method, array $parameters = []): array
    {
        $result = self::bindPerPage($api, $this->limit)->$method(...$parameters);

        if (!\is_array($result)) {
            throw new RuntimeException('Pagination of this endpoint is not supported.');
        }

        return $result;
    }

    /**
     * @param AbstractApi $api
     * @param string      $method
     * @param array       $parameters
     *
     * @throws \Http\Client\Exception
     *
     * @return array
     */
    public function fetchAll(AbstractApi $api, string $method, array $parameters = []): array
    {
        return \iterator_to_array($this->fetchAllLazy($api, $method, $parameters));
    }

    /**
     * @param AbstractApi $api
     * @param string      $method
     * @param array       $parameters
     *
     * @throws \Http\Client\Exception
     *
     * @return \Generator
     */
    public function fetchAllLazy(AbstractApi $api, string $method, array $parameters = []): Generator {
        /** @var mixed $value */
        foreach ($this->fetch($api, $method, $parameters) as $value) {
            yield $value;
        }

        while ($this->hasAfter()) {
            /** @var mixed $value */
            foreach ($this->fetchAfter() as $value) {
                yield $value;
            }
        }
    }

    /**
     * @return bool
     */
    public function hasAfter(): bool {
        return isset($this->pagination['after']);
    }

    /**
     * @throws \Http\Client\Exception
     *
     * @return array
     */
    public function fetchAfter(): array {
        return $this->get('after');
    }

    /**
     * @return bool
     */
    public function hasBefore(): bool {
        return isset($this->pagination['before']);
    }

    /**
     * @throws \Http\Client\Exception
     *
     * @return array
     */
    public function fetchBefore(): array {
        return $this->get('before');
    }

    /**
     * @param string $key
     *
     * @throws \Http\Client\Exception
     *
     * @return array
     */
    private function get(string $key): array {
        $pagination = $this->pagination[$key] ?? null;

        if (null === $pagination) {
            return [];
        }

        $result = $this->client->getHttpClient()->get($pagination);

        $content = ResponseParser::getContent($result);

        if (!\is_array($content)) {
            throw new RuntimeException('Pagination of this endpoint is not supported.');
        }

        return $content;
    }

    /**
     * @param AbstractApi $api
     * @param int         $limit
     *
     * @return AbstractApi
     */
    private static function bindPerPage(AbstractApi $api, int $limit): AbstractApi {
        $closure = Closure::bind(static function (AbstractApi $api) use ($limit): AbstractApi {
            $clone = clone $api;

            $clone->limit = $limit;

            return $clone;
        }, null, AbstractApi::class);

        /** @var AbstractApi */
        return $closure($api);
    }
}
