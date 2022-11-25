<?php

declare(strict_types=1);

namespace DennisKoster\LaravelMaileon\Tests\Unit\Transports;

use DennisKoster\LaravelMaileon\Contracts\MaileonClientInterface;
use DennisKoster\LaravelMaileon\Tests\Unit\AbstractUnitTest;
use DennisKoster\LaravelMaileon\Transports\MaileonTransport;
use Mockery;
use Mockery\MockInterface;
use Symfony\Component\Mime\Email;

class MaileonTransportTest extends AbstractUnitTest
{
    /**
     * @test
     */
    public function it_sends_an_email(): void
    {
        /** @var MaileonClientInterface|MockInterface $maileonClient */
        $maileonClient = Mockery::mock(MaileonClientInterface::class)
            ->shouldReceive('sendEmail')
            ->once()
            ->with(
                'john.doe@example.com',
                'Test email',
                '<p>This is a test</p>'
            )
            ->getMock()
            ->shouldReceive('sendEmail')
            ->once()
            ->with(
                'jane.doe@example.com',
                'Test email',
                '<p>This is a test</p>'
            )
            ->getMock();

        $mail = (new Email())
            ->html('<p>This is a test</p>')
            ->subject('Test email')
            ->from('sender@example.com')
            ->to('john.doe@example.com', 'jane.doe@example.com');

        $transport = new MaileonTransport($maileonClient);

        $transport->send($mail);
    }

    /**
     * @test
     */
    public function it_returns_the_transport_identifier(): void
    {
        $transport = new MaileonTransport(Mockery::mock(MaileonClientInterface::class));

        static::assertSame('maileon', (string) $transport);
    }
}
