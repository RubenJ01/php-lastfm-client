<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Dto\Track;

use Rjds\PhpDto\Attribute\CastTo;
use Rjds\PhpDto\Attribute\MapFrom;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToString;

final readonly class ScrobbleResultDto
{
    use HasToString;

    public function __construct(
        #[MapFrom('track.#text')]
        public string $track,
        #[MapFrom('track.corrected')]
        #[CastTo('bool')]
        public bool $trackCorrected,
        #[MapFrom('artist.#text')]
        public string $artist,
        #[MapFrom('artist.corrected')]
        #[CastTo('bool')]
        public bool $artistCorrected,
        #[MapFrom('album.#text')]
        public string $album,
        #[MapFrom('album.corrected')]
        #[CastTo('bool')]
        public bool $albumCorrected,
        #[MapFrom('albumArtist.#text')]
        public string $albumArtist,
        #[MapFrom('albumArtist.corrected')]
        #[CastTo('bool')]
        public bool $albumArtistCorrected,
        #[CastTo('int')]
        public int $timestamp,
        #[MapFrom('ignoredMessage.code')]
        #[CastTo('int')]
        public int $ignoredCode,
        #[MapFrom('ignoredMessage.#text')]
        public string $ignoredMessage,
    ) {
    }
}
