<?php

declare(strict_types=1);

namespace DennisKoster\LaravelMaileon;

use DennisKoster\LaravelMaileon\Contracts\Factories\RequestFactoryInterface;
use DennisKoster\LaravelMaileon\Contracts\MaileonClientInterface;
use DennisKoster\LaravelMaileon\DataObjects\MaileonConfiguration;
use DennisKoster\LaravelMaileon\Enums\ContactPermissionsEnum;
use DennisKoster\LaravelMaileon\Enums\RequestMethodsEnum;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

class MaileonClient implements MaileonClientInterface
{
    protected ClientInterface $httpClient;
    protected RequestFactoryInterface $requestFactory;
    protected MaileonConfiguration $maileonConfiguration;
    protected ?LoggerInterface $logger;

    /**
     * @param ClientInterface         $httpClient
     * @param RequestFactoryInterface $requestFactory
     * @param MaileonConfiguration    $maileonConfiguration
     * @param LoggerInterface|null    $logger
     */
    public function __construct(
        ClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        MaileonConfiguration $maileonConfiguration,
        ?LoggerInterface $logger = null
    ) {
        $this->httpClient           = $httpClient;
        $this->requestFactory       = $requestFactory;
        $this->maileonConfiguration = $maileonConfiguration;
        $this->logger               = $logger;
    }

    public function sendEmail(
        string $recipientEmail,
        string $subject,
        string $contents
    ): ResponseInterface {
        $requestBody = [
            'typeName' => $this->maileonConfiguration->getContactEvent(),
            'import'   => [
                'contact' => [
                    'email'      => $recipientEmail,
                    'permission' => ContactPermissionsEnum::OTHER,
                ],
            ],
            'content'  => [
                'subject'   => $subject,
                'body_html' => [$contents],
            ],
        ];

        $request = $this->requestFactory->make(
            '/transactions',
            RequestMethodsEnum::POST(),
            $requestBody
        );

        $response = $this->httpClient->sendRequest($request);

        if ($this->maileonConfiguration->enableLogging() && $this->logger) {
            $this->logger->debug('Sending transactional mail request to Maileon.', [
                'request'  => $this->requestToArray($request),
                'response' => $this->responseToArray($response),
            ]);
        }

        return $response;
    }

    /**
     * @param RequestInterface $request
     * @return array<string, mixed>
     */
    protected function requestToArray(RequestInterface $request): array
    {
        $headers = $request->getHeaders();
        
        if ($request->hasHeader('Authorization')) {
            $headers['Authorization'][0] = '******';
        }

        return [
            'uri'     => (string) $request->getUri(),
            'method'  => $request->getMethod(),
            'headers' => $headers,
            'body'    => (string) $request->getBody(),
        ];
    }

    /**
     * @param ResponseInterface $response
     * @return array<string, mixed>
     */
    protected function responseToArray(ResponseInterface $response): array
    {
        return [
            'status'  => $response->getStatusCode(),
            'body'    => (string) $response->getBody(),
            'headers' => $response->getHeaders(),
        ];
    }
}
