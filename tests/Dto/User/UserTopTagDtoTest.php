<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto\User;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpDto\DtoMapper;
use Rjds\PhpLastfmClient\Dto\User\UserTopTagDto;

final class UserTopTagDtoTest extends TestCase
{
    private DtoMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new DtoMapper();
    }

    #[Test]
    public function itMapsFromApiResponse(): void
    {
        $dto = $this->mapper->map(self::tagApiData(), UserTopTagDto::class);

        $this->assertSame('rock', $dto->name);
        $this->assertSame('https://www.last.fm/tag/rock', $dto->url);
        $this->assertSame(100, $dto->count);
    }

    /**
     * @return array<string, mixed>
     */
    private static function tagApiData(): array
    {
        return [
            'name' => 'rock',
            'url' => 'https://www.last.fm/tag/rock',
            'count' => '100',
        ];
    }
}
