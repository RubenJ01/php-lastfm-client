<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Service;

use Rjds\PhpDto\DtoMapper;
use Rjds\PhpLastfmClient\Dto\Common\PaginatedResponse;
use Rjds\PhpLastfmClient\Dto\Common\PaginationDto;
use Rjds\PhpLastfmClient\LastfmClient;

abstract readonly class AbstractService
{
    public function __construct(
        protected LastfmClient $client,
        protected DtoMapper $mapper = new DtoMapper(),
    ) {
    }

    /**
     * Fetch a paginated API endpoint and map the results to DTOs.
     *
     * @template T of object
     *
     * @param string $method The API method (e.g. 'user.getlovedtracks')
     * @param array<string, string|int> $params Query parameters including limit/page
     * @param string $wrapperKey Top-level response key (e.g. 'lovedtracks')
     * @param string $itemsKey Key within the wrapper containing the items (e.g. 'track')
     * @param class-string<T> $dtoClass The DTO class to map each item to
     *
     * @return PaginatedResponse<T>
     */
    protected function paginate(
        string $method,
        array $params,
        string $wrapperKey,
        string $itemsKey,
        string $dtoClass,
    ): PaginatedResponse {
        $response = $this->client->call($method, $params);

        /** @var array<string, mixed> $data */
        $data = $response[$wrapperKey];

        /** @var list<array<string, mixed>> $itemList */
        $itemList = $data[$itemsKey];

        /** @var list<T> $items */
        $items = [];
        foreach ($itemList as $item) {
            $items[] = $this->mapper->map($item, $dtoClass);
        }

        /** @var array<string, mixed> $attrData */
        $attrData = $data['@attr'];
        $pagination = $this->mapper->map($attrData, PaginationDto::class);

        return new PaginatedResponse($items, $pagination);
    }
}
