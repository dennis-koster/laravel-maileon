<?php

declare(strict_types=1);

namespace DennisKoster\LaravelMaileon;

use DennisKoster\LaravelMaileon\Contracts\Factories\RequestFactoryInterface;
use DennisKoster\LaravelMaileon\Contracts\MaileonClientInterface;
use DennisKoster\LaravelMaileon\DataObjects\MaileonConfiguration;
use DennisKoster\LaravelMaileon\Enums\ContactPermissionsEnum;
use DennisKoster\LaravelMaileon\Enums\RequestMethodsEnum;
use Psr\Http\Client\ClientInterface;
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

        $response = $this->httpClient->sendRequest(
            $this->requestFactory->make(
                '/transactions',
                RequestMethodsEnum::POST(),
                $requestBody
            )
        );

        if ($this->maileonConfiguration->logRequests() && $this->logger) {
            $this->logger->debug('Sending transactional mail request to Maileon.', [
                'requestBody' => $requestBody,
                'response'    => $response,
            ]);
        }

        return $response;
    }
}
