<?php

declare(strict_types=1);

namespace DennisKoster\LaravelMaileon\Tests\Unit\Transports;

use DennisKoster\LaravelMaileon\Contracts\MaileonClientInterface;
use DennisKoster\LaravelMaileon\Tests\Unit\AbstractUnitTest;
use DennisKoster\LaravelMaileon\Transports\MaileonTransport;
use Mockery;
use Mockery\MockInterface;
use Swift_Mime_SimpleMessage;

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
                'This is a test'
            )
            ->getMock()
            ->shouldReceive('sendEmail')
            ->once()
            ->with(
                'jane.doe@example.com',
                'Test email',
                'This is a test'
            )
            ->getMock();

        /** @var Swift_Mime_SimpleMessage|MockInterface $swiftMessage */
        $swiftMessage = Mockery::mock(Swift_Mime_SimpleMessage::class)
            ->shouldReceive('getTo')
            ->twice()
            ->andReturn([
                'john.doe@example.com' => 'John Doe',
                'jane.doe@example.com' => 'Jane Doe',
            ])
            ->getMock()
            ->shouldReceive('getSubject')
            ->twice()
            ->andReturn('Test email')
            ->getMock()
            ->shouldReceive('getBody')
            ->twice()
            ->andReturn('This is a test')
            ->getMock()
            ->shouldReceive('getCc')
            ->once()
            ->andReturn([])
            ->getMock()
            ->shouldReceive('getBcc')
            ->once()
            ->andReturn([])
            ->getMock();

        $transport = new MaileonTransport($maileonClient);

        $result = $transport->send($swiftMessage);

        static::assertSame(2, $result);
    }
}
