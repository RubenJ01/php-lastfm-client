<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto\Concerns;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpLastfmClient\Dto\Auth\SessionDto;
use Rjds\PhpLastfmClient\Dto\Common\ImageDto;
use Rjds\PhpLastfmClient\Dto\Common\PaginatedResponse;
use Rjds\PhpLastfmClient\Dto\Common\PaginationDto;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToString;
use Rjds\PhpLastfmClient\Dto\Track\Scrobble;
use Rjds\PhpLastfmClient\Dto\User\UserDto;

final class HasToStringTest extends TestCase
{
    #[Test]
    public function itFormatsSimpleDto(): void
    {
        $dto = new SessionDto('RJ', 'abc123', true);

        $expected = "SessionDto {\n"
            . "  name: \"RJ\"\n"
            . "  key: \"abc123\"\n"
            . "  subscriber: true\n"
            . '}';

        $this->assertSame($expected, (string) $dto);
    }

    #[Test]
    public function itFormatsStringValues(): void
    {
        $dto = new ImageDto('small', 'https://example.com/img.png');

        $output = (string) $dto;

        $this->assertStringContainsString('size: "small"', $output);
        $this->assertStringContainsString('url: "https://example.com/img.png"', $output);
    }

    #[Test]
    public function itFormatsBoolValues(): void
    {
        $trueDto = new SessionDto('RJ', 'key', true);
        $falseDto = new SessionDto('RJ', 'key', false);

        $this->assertStringContainsString('subscriber: true', (string) $trueDto);
        $this->assertStringContainsString('subscriber: false', (string) $falseDto);
    }

    #[Test]
    public function itFormatsIntValues(): void
    {
        $dto = new PaginationDto(1, 50, 7790, 156);

        $output = (string) $dto;

        $this->assertStringContainsString('page: 1', $output);
        $this->assertStringContainsString('perPage: 50', $output);
        $this->assertStringContainsString('total: 7790', $output);
        $this->assertStringContainsString('totalPages: 156', $output);
    }

    #[Test]
    public function itFormatsDateTimeValues(): void
    {
        $dto = new UserDto(
            'RJ',
            'Richard',
            'https://last.fm/user/RJ',
            'UK',
            0,
            true,
            150316,
            12749,
            57066,
            26658,
            0,
            [],
            new \DateTimeImmutable('2002-11-20T12:30:40+00:00'),
            'alum',
        );

        $this->assertStringContainsString('registered: 2002-11-20T12:30:40+00:00', (string) $dto);
    }

    #[Test]
    public function itFormatsArrayOfObjects(): void
    {
        $dto = new UserDto(
            'RJ',
            'Richard',
            'https://last.fm/user/RJ',
            'UK',
            0,
            true,
            150316,
            12749,
            57066,
            26658,
            0,
            [
                new ImageDto('small', 'https://img/s.png'),
                new ImageDto('large', 'https://img/l.png'),
            ],
            new \DateTimeImmutable('2002-11-20T12:30:40+00:00'),
            'alum',
        );

        $output = (string) $dto;

        $this->assertStringContainsString(
            'images: [ImageDto { size: "small", url: "https://img/s.png" },'
            . ' ImageDto { size: "large", url: "https://img/l.png" }]',
            $output,
        );
    }

    #[Test]
    public function itFormatsEmptyArray(): void
    {
        $dto = new UserDto(
            'RJ',
            'Richard',
            'https://last.fm/user/RJ',
            'UK',
            0,
            true,
            150316,
            12749,
            57066,
            26658,
            0,
            [],
            new \DateTimeImmutable('2002-11-20T12:30:40+00:00'),
            'alum',
        );

        $this->assertStringContainsString('images: []', (string) $dto);
    }

    #[Test]
    public function itFormatsNestedObjectInline(): void
    {
        $pagination = new PaginationDto(1, 50, 100, 2);
        $response = new PaginatedResponse([], $pagination);

        $output = (string) $response;

        $this->assertStringContainsString(
            'pagination: PaginationDto { page: 1, perPage: 50, total: 100, totalPages: 2 }',
            $output,
        );
    }

    #[Test]
    public function itFormatsNullValues(): void
    {
        $dto = new Scrobble('Radiohead', 'Creep', 1603112664);

        $output = (string) $dto;

        $this->assertStringContainsString('album: null', $output);
        $this->assertStringContainsString('albumArtist: null', $output);
        $this->assertStringContainsString('trackNumber: null', $output);
        $this->assertStringContainsString('mbid: null', $output);
        $this->assertStringContainsString('duration: null', $output);
        $this->assertStringContainsString('chosenByUser: null', $output);
    }

    #[Test]
    public function itUsesShortClassName(): void
    {
        $dto = new SessionDto('RJ', 'key', true);

        $this->assertStringStartsWith('SessionDto {', (string) $dto);
    }

    #[Test]
    public function itEndsWithClosingBrace(): void
    {
        $dto = new SessionDto('RJ', 'key', true);

        $this->assertStringEndsWith('}', (string) $dto);
    }

    #[Test]
    public function itFormatsFloatValues(): void
    {
        $dto = new class (3.14) {
            use HasToString;

            public function __construct(
                public float $value,
            ) {
            }
        };

        $this->assertStringContainsString('value: 3.14', (string) $dto);
    }

    #[Test]
    public function itImplementsStringable(): void
    {
        $dto = new SessionDto('RJ', 'key', true);

        $this->assertInstanceOf(\Stringable::class, $dto);
    }
}
