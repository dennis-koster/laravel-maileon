<?php

declare(strict_types=1);

namespace DennisKoster\LaravelMaileon\Tests\Unit\Factories;

use DennisKoster\LaravelMaileon\DataObjects\MaileonConfiguration;
use DennisKoster\LaravelMaileon\Enums\RequestMethodsEnum;
use DennisKoster\LaravelMaileon\Factories\RequestFactory;
use DennisKoster\LaravelMaileon\Tests\Unit\AbstractUnitTest;
use Mockery;
use Mockery\MockInterface;

class RequestFactoryTest extends AbstractUnitTest
{
    protected RequestFactory $requestFactory;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var MaileonConfiguration|MockInterface $configuration */
        $configuration = Mockery::mock(MaileonConfiguration::class)
            ->shouldReceive('getApiKey')
            ->once()
            ->andReturn('some-secret-api-key')
            ->getMock()
            ->shouldReceive('getApiUrl')
            ->once()
            ->andReturn('https://foo.bar/com/')
            ->getMock();

        $this->requestFactory = new RequestFactory($configuration);
    }

    /**
     * @test
     */
    public function it_makes_a_request(): void
    {
        $requestMethod = $this->mockRequestMethod('POST');

        $result = $this->requestFactory->make(
            '/transactions',
            $requestMethod,
            ['foo' => 'bar'],
            ['extra' => 'header']
        );

        static::assertSame([
            'Host'          => ['foo.bar'],
            'Content-Type'  => ['application/json'],
            'Authorization' => ['Basic c29tZS1zZWNyZXQtYXBpLWtleQ=='],
            'extra'         => ['header'],
        ], $result->getHeaders());

        static::assertSame('POST', $result->getMethod());

        static::assertSame('https', $result->getUri()->getScheme());
        static::assertSame('foo.bar', $result->getUri()->getHost());
        static::assertSame('/com/transactions', $result->getUri()->getPath());
    }

    /**
     * @test
     */
    public function it_makes_an_uri_for_a_post_request(): void
    {
        /** @var RequestMethodsEnum|MockInterface $requestMethod */
        $requestMethod = Mockery::mock(RequestMethodsEnum::class)
            ->shouldReceive('isNot')
            ->once()
            ->withArgs(function (RequestMethodsEnum $enum) {
                return $enum->is(RequestMethodsEnum::GET());
            })
            ->andReturnTrue()
            ->getMock();

        $result = $this->requestFactory->makeUri('/foo/bar/com', $requestMethod, ['foo' => 'bar']);

        static::assertSame('https://foo.bar/com/foo/bar/com', $result);
    }

    /**
     * @test
     */
    public function it_makes_an_uri_for_a_get_request(): void
    {
        /** @var RequestMethodsEnum|MockInterface $requestMethod */
        $requestMethod = Mockery::mock(RequestMethodsEnum::class)
            ->shouldReceive('isNot')
            ->once()
            ->withArgs(function (RequestMethodsEnum $enum) {
                return $enum->is(RequestMethodsEnum::GET());
            })
            ->andReturnFalse()
            ->getMock();

        $result = $this->requestFactory->makeUri('/foo/bar/com', $requestMethod, ['foo' => 'bar']);

        static::assertSame('https://foo.bar/com/foo/bar/com?foo=bar', $result);
    }

    /**
     * @test
     */
    public function it_authorizes_with_maileon(): void
    {
        $requestMethod = $this->mockRequestMethod('POST');

        $this->requestFactory->authorize(Mockery::mock(MaileonConfiguration::class, [
            'getApiKey' => 'some-other-api-key',
        ]));

        $result = $this->requestFactory->make(
            '/transactions',
            $requestMethod,
            ['foo' => 'bar'],
            ['extra' => 'header']
        );

        static::assertSame([
            'Basic c29tZS1vdGhlci1hcGkta2V5',
        ], $result->getHeader('Authorization'));
    }

    /**
     * @test
     */
    public function it_adds_a_default_header(): void
    {
        $headers = $this->requestFactory->addDefaultHeaders(['some' => 'extra-header']);

        static::assertSame([
            'Content-Type'  => 'application/json',
            'Authorization' => 'Basic c29tZS1zZWNyZXQtYXBpLWtleQ==',
            'some'          => 'extra-header',
        ], $headers);
    }

    /**
     * @param string $httpMethod
     * @return RequestMethodsEnum|MockInterface
     */
    protected function mockRequestMethod(string $httpMethod)
    {
        /** @var RequestMethodsEnum|MockInterface $requestMethod */
        $requestMethod        = Mockery::mock(RequestMethodsEnum::class)
            ->shouldReceive('isNot')
            ->once()
            ->withArgs(function (RequestMethodsEnum $enum) {
                return $enum->is(RequestMethodsEnum::GET());
            })
            ->andReturn($httpMethod === 'POST')
            ->getMock()
            ->shouldReceive('is')
            ->once()
            ->withArgs(function (RequestMethodsEnum $enum) {
                return $enum->is(RequestMethodsEnum::GET());
            })
            ->andReturn($httpMethod !== 'POST')
            ->getMock();
        $requestMethod->value = $httpMethod;

        return $requestMethod;
    }
}
