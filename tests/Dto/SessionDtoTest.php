<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpDto\DtoMapper;
use Rjds\PhpLastfmClient\Dto\SessionDto;

final class SessionDtoTest extends TestCase
{
    #[Test]
    public function itMapsFromApiResponse(): void
    {
        $mapper = new DtoMapper();

        $dto = $mapper->map([
            'name' => 'RubenJ01',
            'key' => 'abc123sessionkey',
            'subscriber' => '0',
        ], SessionDto::class);

        $this->assertSame('RubenJ01', $dto->name);
        $this->assertSame('abc123sessionkey', $dto->key);
        $this->assertFalse($dto->subscriber);
    }

    #[Test]
    public function itMapsSubscriberTrue(): void
    {
        $mapper = new DtoMapper();

        $dto = $mapper->map([
            'name' => 'RubenJ01',
            'key' => 'abc123',
            'subscriber' => '1',
        ], SessionDto::class);

        $this->assertTrue($dto->subscriber);
    }
}
