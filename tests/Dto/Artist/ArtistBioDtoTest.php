<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Dto\Artist;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpLastfmClient\Dto\Artist\ArtistBioDto;

final class ArtistBioDtoTest extends TestCase
{
    #[Test]
    public function itHoldsBioFields(): void
    {
        $bio = new ArtistBioDto('01 Jan 2020', 'Summary', 'Content');

        $this->assertSame('01 Jan 2020', $bio->published);
        $this->assertSame('Summary', $bio->summary);
        $this->assertSame('Content', $bio->content);
    }
}
