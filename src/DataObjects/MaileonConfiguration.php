<?php

declare(strict_types=1);

namespace DennisKoster\LaravelMaileon\DataObjects;

class MaileonConfiguration
{
    protected string $apiUrl;
    protected string $apiKey;
    protected string $contactEvent;
    protected ?string $httpClient;

    public function __construct(
        string $apiUrl,
        string $apiKey,
        string $contactEvent,
        ?string $httpClient
    ) {
        $this->apiUrl       = $apiUrl;
        $this->apiKey       = $apiKey;
        $this->contactEvent = $contactEvent;
        $this->httpClient   = $httpClient;
    }

    public function getApiUrl(): string
    {
        return $this->apiUrl;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getContactEvent(): string
    {
        return $this->contactEvent;
    }

    public function getHttpClient(): ?string
    {
        return $this->httpClient;
    }
}
