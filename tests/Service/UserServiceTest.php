<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Tests\Service;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpLastfmClient\Dto\UserDto;
use Rjds\PhpLastfmClient\Http\HttpClientInterface;
use Rjds\PhpLastfmClient\LastfmClient;

final class UserServiceTest extends TestCase
{
    #[Test]
    public function itReturnsUserDto(): void
    {
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('get')
            ->willReturn((string) json_encode([
                'user' => [
                    'name' => 'RJ',
                    'realname' => 'Richard Jones',
                    'url' => 'https://www.last.fm/user/RJ',
                    'country' => 'United Kingdom',
                    'age' => '0',
                    'subscriber' => '1',
                    'playcount' => '150316',
                    'artist_count' => '12749',
                    'track_count' => '57066',
                    'album_count' => '26658',
                    'playlists' => '0',
                    'image' => [
                        ['size' => 'small', '#text' => 'https://lastfm.freetls.fastly.net/i/u/34s/image.png'],
                    ],
                    'registered' => ['unixtime' => '1037793040', '#text' => 1037793040],
                    'type' => 'alum',
                ],
            ]));

        $client = new LastfmClient('test-api-key', $httpClient);
        $user = $client->user()->getInfo('rj');

        $this->assertInstanceOf(UserDto::class, $user);
        $this->assertSame('RJ', $user->name);
        $this->assertSame('Richard Jones', $user->realname);
        $this->assertSame(150316, $user->playcount);
        $this->assertTrue($user->subscriber);
    }

    #[Test]
    public function itCallsCorrectApiMethod(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('get')
            ->with($this->callback(function (string $url): bool {
                $this->assertIsString(parse_url($url, PHP_URL_QUERY));
                parse_str((string) parse_url($url, PHP_URL_QUERY), $params);
                $this->assertSame('user.getinfo', $params['method']);
                $this->assertSame('testuser', $params['user']);

                return true;
            }))
            ->willReturn((string) json_encode([
                'user' => [
                    'name' => 'testuser',
                    'realname' => 'Test User',
                    'url' => 'https://www.last.fm/user/testuser',
                    'country' => '',
                    'age' => '0',
                    'subscriber' => '0',
                    'playcount' => '0',
                    'artist_count' => '0',
                    'track_count' => '0',
                    'album_count' => '0',
                    'playlists' => '0',
                    'image' => [],
                    'registered' => ['unixtime' => '0', '#text' => 0],
                    'type' => 'user',
                ],
            ]));

        $client = new LastfmClient('test-api-key', $httpClient);
        $client->user()->getInfo('testuser');
    }
}
