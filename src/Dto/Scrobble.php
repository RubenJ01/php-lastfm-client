<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Dto;

/**
 * Represents a single scrobble to submit.
 */
final readonly class Scrobble
{
    public function __construct(
        public string $artist,
        public string $track,
        public int $timestamp,
        public ?string $album = null,
        public ?string $albumArtist = null,
        public ?int $trackNumber = null,
        public ?string $mbid = null,
        public ?int $duration = null,
        public ?bool $chosenByUser = null,
    ) {
    }

    /**
     * Convert to API parameters for a given batch index.
     *
     * @return array<string, string>
     */
    public function toParams(int $index): array
    {
        $params = [
            "artist[{$index}]" => $this->artist,
            "track[{$index}]" => $this->track,
            "timestamp[{$index}]" => (string) $this->timestamp,
        ];

        if ($this->album !== null) {
            $params["album[{$index}]"] = $this->album;
        }

        if ($this->albumArtist !== null) {
            $params["albumArtist[{$index}]"] = $this->albumArtist;
        }

        if ($this->trackNumber !== null) {
            $params["trackNumber[{$index}]"] = (string) $this->trackNumber;
        }

        if ($this->mbid !== null) {
            $params["mbid[{$index}]"] = $this->mbid;
        }

        if ($this->duration !== null) {
            $params["duration[{$index}]"] = (string) $this->duration;
        }

        if ($this->chosenByUser !== null) {
            $params["chosenByUser[{$index}]"] = $this->chosenByUser ? '1' : '0';
        }

        return $params;
    }
}
