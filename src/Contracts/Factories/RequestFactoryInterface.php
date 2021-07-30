<?php

declare(strict_types=1);

namespace DennisKoster\LaravelMaileon\Contracts\Factories;

use DennisKoster\LaravelMaileon\Enums\RequestMethodsEnum;
use Psr\Http\Message\RequestInterface;

interface RequestFactoryInterface
{
    public function make(string $uri, RequestMethodsEnum $method, array $data = [], array $headers = []): RequestInterface;

    public function makeUri(string $uri, RequestMethodsEnum $method, array $data = []): string;
}
