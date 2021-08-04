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

class MaileonClient implements MaileonClientInterface
{
    protected ClientInterface $httpClient;
    protected RequestFactoryInterface $requestFactory;
    protected MaileonConfiguration $maileonConfiguration;

    /**
     * @param ClientInterface         $httpClient
     * @param RequestFactoryInterface $requestFactory
     * @param MaileonConfiguration    $maileonConfiguration
     */
    public function __construct(
        ClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        MaileonConfiguration $maileonConfiguration
    ) {
        $this->httpClient           = $httpClient;
        $this->requestFactory       = $requestFactory;
        $this->maileonConfiguration = $maileonConfiguration;
    }

    public function sendEmail(
        string $recipientEmail,
        string $subject,
        string $contents
    ): ResponseInterface {
        return $this->httpClient->sendRequest(
            $this->requestFactory->make(
                '/transactions',
                RequestMethodsEnum::POST(),
                [
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
                ]
            )
        );
    }
}
