<?php

declare(strict_types=1);

namespace Rjds\PhpLastfmClient\Exception;

final class LastfmApiException extends \RuntimeException
{
    public function __construct(
        string $message,
        private readonly int $apiErrorCode,
    ) {
        parent::__construct($message, $apiErrorCode);
    }

    public function getApiErrorCode(): int
    {
        return $this->apiErrorCode;
    }
}
