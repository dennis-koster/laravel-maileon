<?php

declare(strict_types=1);

namespace DennisKoster\LaravelMaileon\DataObjects;

class MaileonConfiguration
{
    protected string $apiUrl;
    protected string $apiKey;
    protected string $contactEvent;
    protected ?string $httpClient;
    protected ?string $logger;
    protected bool $debugMode;

    public function __construct(
        string $apiUrl,
        string $apiKey,
        string $contactEvent,
        ?string $httpClient,
        ?string $logger = null,
        bool $debugMode = false
    ) {
        $this->apiUrl       = $apiUrl;
        $this->apiKey       = $apiKey;
        $this->contactEvent = $contactEvent;
        $this->httpClient   = $httpClient;
        $this->logger       = $logger;
        $this->debugMode    = $debugMode;
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

    public function getLogger(): ?string
    {
        return $this->logger;
    }

    public function debugMode(): bool
    {
        return $this->debugMode;
    }
}
