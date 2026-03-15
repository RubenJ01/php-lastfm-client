<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Service;

use Rjds\PhpDto\DtoMapper;
use Rjds\PhpLastfmClient\Dto\Scrobble;
use Rjds\PhpLastfmClient\Dto\ScrobbleResponseDto;
use Rjds\PhpLastfmClient\Dto\ScrobbleResultDto;
use Rjds\PhpLastfmClient\LastfmClient;

final readonly class TrackService
{
    public function __construct(
        private LastfmClient $client,
        private DtoMapper $mapper = new DtoMapper(),
    ) {
    }

    /**
     * Scrobble a single track.
     *
     * @see https://www.last.fm/api/show/track.scrobble
     */
    public function scrobble(Scrobble $scrobble): ScrobbleResponseDto
    {
        return $this->scrobbleBatch([$scrobble]);
    }

    /**
     * Scrobble a batch of tracks (up to 50).
     *
     * @param list<Scrobble> $scrobbles The scrobbles to submit
     *
     * @throws \InvalidArgumentException when the batch is empty or exceeds 50
     *
     * @see https://www.last.fm/api/show/track.scrobble
     */
    public function scrobbleBatch(array $scrobbles): ScrobbleResponseDto
    {
        if (count($scrobbles) === 0) {
            throw new \InvalidArgumentException('At least one scrobble is required.');
        }

        if (count($scrobbles) > 50) {
            throw new \InvalidArgumentException(
                'A maximum of 50 scrobbles can be sent per batch.'
            );
        }

        $params = [];
        foreach ($scrobbles as $index => $scrobble) {
            $params = array_merge($params, $scrobble->toParams($index));
        }

        $response = $this->client->callAuthenticated('track.scrobble', $params);

        /** @var array<string, mixed> $scrobblesData */
        $scrobblesData = $response['scrobbles'];

        /** @var array{accepted: int, ignored: int} $attr */
        $attr = $scrobblesData['@attr'];

        $accepted = $attr['accepted'];
        $ignored = $attr['ignored'];

        /** @var array<string, mixed>|list<array<string, mixed>> $scrobbleData */
        $scrobbleData = $scrobblesData['scrobble'];

        // Single scrobble returns an object, batch returns an array
        if (!array_is_list($scrobbleData)) {
            $scrobbleData = [$scrobbleData];
        }

        /** @var list<ScrobbleResultDto> $results */
        $results = [];
        foreach ($scrobbleData as $item) {
            /** @var array<string, mixed> $item */
            $results[] = $this->mapper->map($item, ScrobbleResultDto::class);
        }

        return new ScrobbleResponseDto($accepted, $ignored, $results);
    }
}
