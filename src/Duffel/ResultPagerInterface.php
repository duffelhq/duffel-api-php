<?php

declare(strict_types=1);

namespace Duffel;

use Generator;
use Duffel\Api\AbstractApi;

interface ResultPagerInterface {
    /**
     * @param AbstractApi $api
     * @param string      $method
     * @param array       $parameters
     *
     * @throws \Http\Client\Exception
     *
     * @return array
     */
    public function fetch(AbstractApi $api, string $method, array $parameters = []): array;

    /**
     * @param AbstractApi $api
     * @param string      $method
     * @param array       $parameters
     *
     * @throws \Http\Client\Exception
     *
     * @return array
     */
    public function fetchAll(AbstractApi $api, string $method, array $parameters = []): array;

    /**
     * @param AbstractApi $api
     * @param string      $method
     * @param array       $parameters
     *
     * @throws \Http\Client\Exception
     *
     * @return \Generator
     */
    public function fetchAllLazy(AbstractApi $api, string $method, array $parameters = []): Generator;

    /**
     * @return bool
     */
    public function hasAfter(): bool;

    /**
     * @throws \Http\Client\Exception
     *
     * @return array
     */
    public function fetchAfter(): array;

    /**
     * @return bool
     */
    public function hasBefore(): bool;

    /**
     * @throws \Http\Client\Exception
     *
     * @return array
     */
    public function fetchBefore(): array;
}
