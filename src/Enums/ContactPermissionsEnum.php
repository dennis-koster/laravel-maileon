<?php

declare(strict_types=1);

namespace DennisKoster\LaravelMaileon\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static NONE()
 * @method static static SINGLE_OPT_IN()
 * @method static static CONFIRMED_OPT_IN()
 * @method static static DOUBLE_OPT_IN()
 * @method static static DOUBLE_OPT_IN_PLUS()
 * @method static static OTHER()
 */
class ContactPermissionsEnum extends Enum
{
    public const NONE               = 1;
    public const SINGLE_OPT_IN      = 2;
    public const CONFIRMED_OPT_IN   = 3;
    public const DOUBLE_OPT_IN      = 4;
    public const DOUBLE_OPT_IN_PLUS = 5;
    public const OTHER              = 6;

}
