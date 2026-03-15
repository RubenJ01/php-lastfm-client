<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Service;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpLastfmClient\Dto\Scrobble;
use Rjds\PhpLastfmClient\Dto\ScrobbleResponseDto;
use Rjds\PhpLastfmClient\Http\HttpClientInterface;
use Rjds\PhpLastfmClient\LastfmClient;

final class TrackServiceTest extends TestCase
{
    private function createAuthenticatedClient(
        HttpClientInterface $httpClient,
    ): LastfmClient {
        return new LastfmClient(
            apiKey: 'test-key',
            httpClient: $httpClient,
            apiSecret: 'test-secret',
            sessionKey: 'test-sk',
        );
    }

    #[Test]
    public function itScrobblesSingleTrack(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('post')
            ->willReturn((string) json_encode(
                self::singleScrobbleResponse()
            ));

        $client = $this->createAuthenticatedClient($httpClient);
        $result = $client->track()->scrobble(new Scrobble(
            artist: 'Test Artist',
            track: 'Test Track',
            timestamp: 1287140447,
        ));

        $this->assertInstanceOf(ScrobbleResponseDto::class, $result);
        $this->assertSame(1, $result->accepted);
        $this->assertSame(0, $result->ignored);
        $this->assertCount(1, $result->scrobbles);
        $this->assertSame('Test Track', $result->scrobbles[0]->track);
        $this->assertSame('Test Artist', $result->scrobbles[0]->artist);
    }

    #[Test]
    public function itScrobblesBatchOfTracks(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('post')
            ->willReturn((string) json_encode(
                self::batchScrobbleResponse()
            ));

        $client = $this->createAuthenticatedClient($httpClient);
        $result = $client->track()->scrobbleBatch([
            new Scrobble('Artist 0', 'Track 0', 1287141093),
            new Scrobble('Artist 1', 'Track 1', 1287141093),
        ]);

        $this->assertSame(2, $result->accepted);
        $this->assertSame(0, $result->ignored);
        $this->assertCount(2, $result->scrobbles);
        $this->assertSame('Track 0', $result->scrobbles[0]->track);
        $this->assertSame('Track 1', $result->scrobbles[1]->track);
    }

    #[Test]
    public function itSendsCorrectParametersForBatch(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('post')
            ->with(
                $this->anything(),
                $this->callback(function (array $data): bool {
                    $this->assertSame('track.scrobble', $data['method']);
                    $this->assertSame('Artist A', $data['artist[0]']);
                    $this->assertSame('Track A', $data['track[0]']);
                    $this->assertSame('100', $data['timestamp[0]']);
                    $this->assertSame('Artist B', $data['artist[1]']);
                    $this->assertSame('Track B', $data['track[1]']);
                    $this->assertSame('200', $data['timestamp[1]']);

                    return true;
                }),
            )
            ->willReturn((string) json_encode(
                self::batchScrobbleResponse()
            ));

        $client = $this->createAuthenticatedClient($httpClient);
        $client->track()->scrobbleBatch([
            new Scrobble('Artist A', 'Track A', 100),
            new Scrobble('Artist B', 'Track B', 200),
        ]);
    }

    #[Test]
    public function itThrowsOnEmptyBatch(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $client = $this->createAuthenticatedClient($httpClient);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('At least one scrobble is required');

        $client->track()->scrobbleBatch([]);
    }

    #[Test]
    public function itAllowsExactlyFiftyScrobbles(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('post')
            ->willReturn((string) json_encode(
                self::batchScrobbleResponse()
            ));

        $client = $this->createAuthenticatedClient($httpClient);

        $scrobbles = [];
        for ($i = 0; $i < 50; $i++) {
            $scrobbles[] = new Scrobble('Artist', 'Track', 100 + $i);
        }

        $result = $client->track()->scrobbleBatch($scrobbles);

        $this->assertInstanceOf(ScrobbleResponseDto::class, $result);
    }

    #[Test]
    public function itThrowsWhenBatchExceedsFifty(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $client = $this->createAuthenticatedClient($httpClient);

        $scrobbles = [];
        for ($i = 0; $i < 51; $i++) {
            $scrobbles[] = new Scrobble('Artist', 'Track', 100);
        }

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('maximum of 50');

        $client->track()->scrobbleBatch($scrobbles);
    }

    /**
     * @return array<string, mixed>
     */
    private static function singleScrobbleResponse(): array
    {
        return [
            'scrobbles' => [
                'scrobble' => [
                    'track' => [
                        'corrected' => '0',
                        '#text' => 'Test Track',
                    ],
                    'artist' => [
                        'corrected' => '0',
                        '#text' => 'Test Artist',
                    ],
                    'album' => ['corrected' => '0', '#text' => ''],
                    'albumArtist' => [
                        'corrected' => '0',
                        '#text' => '',
                    ],
                    'timestamp' => '1287140447',
                    'ignoredMessage' => [
                        'code' => '0',
                        '#text' => '',
                    ],
                ],
                '@attr' => [
                    'accepted' => 1,
                    'ignored' => 0,
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function batchScrobbleResponse(): array
    {
        return [
            'scrobbles' => [
                'scrobble' => [
                    self::scrobbleItem('Track 0', 'Artist 0'),
                    self::scrobbleItem('Track 1', 'Artist 1'),
                ],
                '@attr' => [
                    'accepted' => 2,
                    'ignored' => 0,
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function scrobbleItem(
        string $track,
        string $artist,
    ): array {
        return [
            'track' => ['corrected' => '0', '#text' => $track],
            'artist' => ['corrected' => '0', '#text' => $artist],
            'album' => ['corrected' => '0', '#text' => ''],
            'albumArtist' => ['corrected' => '0', '#text' => ''],
            'timestamp' => '1287141093',
            'ignoredMessage' => ['code' => '0', '#text' => ''],
        ];
    }
}
