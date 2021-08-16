<?php

declare(strict_types=1);

namespace DennisKoster\LaravelMaileon\Providers;

use DennisKoster\LaravelMaileon\Contracts\Factories\RequestFactoryInterface;
use DennisKoster\LaravelMaileon\Contracts\MaileonClientInterface;
use DennisKoster\LaravelMaileon\DataObjects\MaileonConfiguration;
use DennisKoster\LaravelMaileon\MaileonClient;
use DennisKoster\LaravelMaileon\Factories\RequestFactory;
use Http\Discovery\Psr18ClientDiscovery;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;

class LaravelMaileonServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            dirname(__DIR__, 2) . DIRECTORY_SEPARATOR
            . 'config' . DIRECTORY_SEPARATOR
            . 'laravel-maileon.php',
            'laravel-maileon'
        );

        $this->registerMaileonConfiguration()
            ->registerRequestFactory()
            ->registerMaileonClient()
            ->registerCustomMailServiceProvider()
            ->setMaileonTransport();
    }

    protected function registerMaileonConfiguration(): self
    {
        $this->app->singleton(MaileonConfiguration::class, function (Container $container) {
            $config = $container->make(Config::class);

            return new MaileonConfiguration(
                $config->get('laravel-maileon.api-url'),
                $config->get('laravel-maileon.api-key'),
                $config->get('laravel-maileon.contact-event'),
                $config->get('laravel-maileon.http-client'),
                $config->get('laravel-maileon.logger'),
                (boolean) $config->get('laravel-maileon.enable-logging'),
            );
        });

        return $this;
    }

    protected function registerRequestFactory(): self
    {
        $this->app->singleton(RequestFactoryInterface::class, RequestFactory::class);

        return $this;
    }

    protected function registerCustomMailServiceProvider(): self
    {
        $this->app->register(MaileonMailServiceProvider::class);

        return $this;
    }

    protected function registerMaileonClient(): self
    {
        $this->app->singleton(MaileonClientInterface::class, function (Container $container) {
            return new MaileonClient(
                $this->getHttpClient(),
                $container->make(RequestFactoryInterface::class),
                $container->make(MaileonConfiguration::class)
            );
        });

        return $this;
    }

    protected function getHttpClient(): ClientInterface
    {
        /** @var MaileonConfiguration $config */
        $config = $this->app->make(MaileonConfiguration::class);

        if ($config->getHttpClient()) {
            return $this->app->make($config->getHttpClient());
        }

        return Psr18ClientDiscovery::find();
    }

    protected function getLogger(): ?LoggerInterface
    {
        /** @var MaileonConfiguration $config */
        $config = $this->app->make(MaileonConfiguration::class);

        if ($config->getLogger()) {
            return $this->app->make($config->getLogger());
        }

        try {
            return $this->app->make(LoggerInterface::class);
        } catch (BindingResolutionException $exception) {
            return null;
        }
    }

    protected function setMaileonTransport(): self
    {
        $config = $this->app->make(Config::class);

        $config->set('mail.mailers.maileon.transport', 'maileon');

        return $this;
    }

    public function boot(): void
    {
        $this->publishes([
            dirname(__DIR__, 2) . DIRECTORY_SEPARATOR
            . 'config' . DIRECTORY_SEPARATOR
            . 'laravel-maileon.php' => config_path('laravel-maileon.php'),
        ]);
    }
}
