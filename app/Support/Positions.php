<?php

namespace App\Support;

/**
 * 4 profils métiers du cabinet dentaire.
 */
class Positions
{
    public const DENTIST = 'Dentiste';
    public const DENTAL_ASSISTANT = 'Assistant dentaire';
    public const ADMIN_ASSISTANT = 'Assistant administratif';
    public const OPERATIONS_DIRECTOR = "Directrice d'exploitation";

    public const ALL = [
        self::DENTIST,
        self::DENTAL_ASSISTANT,
        self::ADMIN_ASSISTANT,
        self::OPERATIONS_DIRECTOR,
    ];

    public static function list(): array
    {
        return self::ALL;
    }
}
