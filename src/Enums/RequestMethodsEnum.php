<?php

declare(strict_types=1);

namespace DennisKoster\LaravelMaileon\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static DELETE()
 * @method static static GET()
 * @method static static PATCH()
 * @method static static POST()
 * @method static static PUT()
 */
class RequestMethodsEnum extends Enum
{
    public const DELETE = 'DELETE';
    public const GET    = 'GET';
    public const PATCH  = 'PATCH';
    public const POST   = 'POST';
    public const PUT    = 'PUT';
}
