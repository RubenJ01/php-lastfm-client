<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Service;

use Rjds\PhpDto\DtoMapper;
use Rjds\PhpLastfmClient\Dto\User\UserDto;
use Rjds\PhpLastfmClient\LastfmClient;

final readonly class UserService
{
    public function __construct(
        private LastfmClient $client,
        private DtoMapper $mapper = new DtoMapper(),
    ) {
    }

    /**
     * Get information about a user profile.
     *
     * @see https://lastfm-docs.github.io/api-docs/user/getInfo/
     */
    public function getInfo(string $user): UserDto
    {
        $response = $this->client->call('user.getinfo', ['user' => $user]);

        /** @var array<string, mixed> $userData */
        $userData = $response['user'];

        return $this->mapper->map($userData, UserDto::class);
    }
}
