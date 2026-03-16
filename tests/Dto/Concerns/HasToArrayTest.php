<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto\Concerns;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpLastfmClient\Dto\Auth\SessionDto;
use Rjds\PhpLastfmClient\Dto\Common\ImageDto;
use Rjds\PhpLastfmClient\Dto\Common\PaginatedResponse;
use Rjds\PhpLastfmClient\Dto\Common\PaginationDto;
use Rjds\PhpLastfmClient\Dto\Concerns\HasToArray;
use Rjds\PhpLastfmClient\Dto\Track\Scrobble;
use Rjds\PhpLastfmClient\Dto\User\UserDto;

final class HasToArrayTest extends TestCase
{
    #[Test]
    public function itConvertsSimpleDto(): void
    {
        $dto = new SessionDto('RJ', 'abc123', true);

        $this->assertSame([
            'name' => 'RJ',
            'key' => 'abc123',
            'subscriber' => true,
        ], $dto->toArray());
    }

    #[Test]
    public function itConvertsStringValues(): void
    {
        $dto = new ImageDto('small', 'https://example.com/img.png');

        $result = $dto->toArray();

        $this->assertSame('small', $result['size']);
        $this->assertSame('https://example.com/img.png', $result['url']);
    }

    #[Test]
    public function itConvertsBoolValues(): void
    {
        $trueDto = new SessionDto('RJ', 'key', true);
        $falseDto = new SessionDto('RJ', 'key', false);

        $this->assertTrue($trueDto->toArray()['subscriber']);
        $this->assertFalse($falseDto->toArray()['subscriber']);
    }

    #[Test]
    public function itConvertsIntValues(): void
    {
        $dto = new PaginationDto(1, 50, 7790, 156);

        $result = $dto->toArray();

        $this->assertSame(1, $result['page']);
        $this->assertSame(50, $result['perPage']);
        $this->assertSame(7790, $result['total']);
        $this->assertSame(156, $result['totalPages']);
    }

    #[Test]
    public function itConvertsFloatValues(): void
    {
        $dto = new class (3.14) {
            use HasToArray;

            public function __construct(
                public float $value,
            ) {
            }
        };

        $this->assertSame(3.14, $dto->toArray()['value']);
    }

    #[Test]
    public function itConvertsDateTimeToIso8601(): void
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

        $this->assertSame('2002-11-20T12:30:40+00:00', $dto->toArray()['registered']);
    }

    #[Test]
    public function itConvertsNestedObjectToArray(): void
    {
        $pagination = new PaginationDto(1, 50, 100, 2);
        $response = new PaginatedResponse([], $pagination);

        $result = $response->toArray();

        $this->assertSame([
            'page' => 1,
            'perPage' => 50,
            'total' => 100,
            'totalPages' => 2,
        ], $result['pagination']);
    }

    #[Test]
    public function itConvertsArrayOfObjectsToArrayOfArrays(): void
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

        $result = $dto->toArray();

        $this->assertSame([
            ['size' => 'small', 'url' => 'https://img/s.png'],
            ['size' => 'large', 'url' => 'https://img/l.png'],
        ], $result['images']);
    }

    #[Test]
    public function itConvertsEmptyArrayToEmptyArray(): void
    {
        $response = new PaginatedResponse([], new PaginationDto(1, 50, 0, 0));

        $this->assertSame([], $response->toArray()['items']);
    }

    #[Test]
    public function itConvertsNullValues(): void
    {
        $dto = new Scrobble('Radiohead', 'Creep', 1603112664);

        $result = $dto->toArray();

        $this->assertNull($result['album']);
        $this->assertNull($result['albumArtist']);
        $this->assertNull($result['trackNumber']);
        $this->assertNull($result['mbid']);
        $this->assertNull($result['duration']);
        $this->assertNull($result['chosenByUser']);
    }

    #[Test]
    public function itReturnsAssociativeArray(): void
    {
        $dto = new SessionDto('RJ', 'key', true);

        $result = $dto->toArray();

        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('key', $result);
        $this->assertArrayHasKey('subscriber', $result);
        $this->assertCount(3, $result);
    }

    #[Test]
    public function itIsJsonEncodable(): void
    {
        $dto = new SessionDto('RJ', 'abc123', true);

        $json = json_encode($dto->toArray());

        $this->assertSame('{"name":"RJ","key":"abc123","subscriber":true}', $json);
    }
}
