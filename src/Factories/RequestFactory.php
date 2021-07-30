<?php

declare(strict_types=1);

namespace DennisKoster\LaravelMaileon\Factories;

use DennisKoster\LaravelMaileon\Contracts\Factories\RequestFactoryInterface;
use DennisKoster\LaravelMaileon\DataObjects\MaileonConfiguration;
use DennisKoster\LaravelMaileon\Enums\RequestMethodsEnum;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;

class RequestFactory implements RequestFactoryInterface
{
    protected string $baseUri;

    /**
     * @var array<string, mixed>
     */
    protected array $defaultHeaders = [
        'Content-Type' => 'application/json',
    ];

    public function __construct(MaileonConfiguration $maileonConfiguration)
    {
        $this->baseUri = rtrim($maileonConfiguration->getApiUrl(), '/');

        $this->authorize($maileonConfiguration);
    }

    public function authorize(MaileonConfiguration $maileonConfiguration): void
    {
        $this->defaultHeaders['Authorization'] = 'Basic ' . base64_encode($maileonConfiguration->getApiKey());
    }

    public function make(
        string $uri,
        RequestMethodsEnum $method,
        array $data = [],
        array $headers = []
    ): RequestInterface {
        return new Request(
            $method->value,
            $this->makeUri($uri, $method, $data),
            $this->addDefaultHeaders($headers),
            $method->is(RequestMethodsEnum::GET()) ? null : json_encode($data),
        );
    }

    /**
     * @param array $headers
     * @return array<string, mixed>
     */
    public function addDefaultHeaders(array $headers): array
    {
        return array_merge(
            $this->defaultHeaders,
            $headers
        );
    }

    public function makeUri(string $uri, RequestMethodsEnum $method, array $data = []): string
    {
        // If $uri is not an absolute url, prepend it with the base url property
        if (preg_match('/^https?:\/\//', $uri) !== 1) {
            $uri = $this->baseUri . '/' . ltrim($uri, '/');
        }

        if ($method->isNot(RequestMethodsEnum::GET())) {
            return $uri;
        }

        if (empty($data)) {
            return $uri;
        }

        return $uri . '?' . http_build_query($data);
    }
}
