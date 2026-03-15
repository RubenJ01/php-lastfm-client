<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto\Track;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpDto\DtoMapper;
use Rjds\PhpLastfmClient\Dto\Track\ScrobbleResultDto;

final class ScrobbleResultDtoTest extends TestCase
{
    private DtoMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new DtoMapper();
    }

    #[Test]
    public function itMapsFromApiResponse(): void
    {
        $data = [
            'track' => ['corrected' => '0', '#text' => 'Test Track'],
            'artist' => ['corrected' => '0', '#text' => 'Test Artist'],
            'album' => ['corrected' => '0', '#text' => ''],
            'albumArtist' => ['corrected' => '0', '#text' => ''],
            'timestamp' => '1287140447',
            'ignoredMessage' => ['code' => '0', '#text' => ''],
        ];

        $dto = $this->mapper->map($data, ScrobbleResultDto::class);

        $this->assertSame('Test Track', $dto->track);
        $this->assertFalse($dto->trackCorrected);
        $this->assertSame('Test Artist', $dto->artist);
        $this->assertFalse($dto->artistCorrected);
        $this->assertSame('', $dto->album);
        $this->assertFalse($dto->albumCorrected);
        $this->assertSame('', $dto->albumArtist);
        $this->assertFalse($dto->albumArtistCorrected);
        $this->assertSame(1287140447, $dto->timestamp);
        $this->assertSame(0, $dto->ignoredCode);
        $this->assertSame('', $dto->ignoredMessage);
    }

    #[Test]
    public function itDetectsCorrections(): void
    {
        $data = [
            'track' => ['corrected' => '1', '#text' => 'Corrected Track'],
            'artist' => ['corrected' => '1', '#text' => 'Corrected Artist'],
            'album' => ['corrected' => '0', '#text' => 'Album'],
            'albumArtist' => ['corrected' => '0', '#text' => ''],
            'timestamp' => '1287140447',
            'ignoredMessage' => ['code' => '0', '#text' => ''],
        ];

        $dto = $this->mapper->map($data, ScrobbleResultDto::class);

        $this->assertTrue($dto->trackCorrected);
        $this->assertTrue($dto->artistCorrected);
    }

    #[Test]
    public function itMapsIgnoredMessage(): void
    {
        $data = [
            'track' => ['corrected' => '0', '#text' => 'Test Track'],
            'artist' => ['corrected' => '0', '#text' => 'Test Artist'],
            'album' => ['corrected' => '0', '#text' => ''],
            'albumArtist' => ['corrected' => '0', '#text' => ''],
            'timestamp' => '1287140447',
            'ignoredMessage' => ['code' => '3', '#text' => 'Timestamp too old'],
        ];

        $dto = $this->mapper->map($data, ScrobbleResultDto::class);

        $this->assertSame(3, $dto->ignoredCode);
        $this->assertSame('Timestamp too old', $dto->ignoredMessage);
    }
}
