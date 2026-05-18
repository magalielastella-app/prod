<?php

namespace App\Support;

/**
 * Profils métiers du Cabinet Dentaire de l'Obiou.
 */
class Positions
{
    public const DENTIST = 'Dentiste';
    public const DENTAL_ASSISTANT = 'Assistant dentaire';
    public const ADMIN_ASSISTANT = 'Assistant administratif';
    public const OPERATIONS_DIRECTOR = "Directrice d'exploitation";
    public const CLINICAL_REFERENT = 'Référente clinique';
    public const ADMIN_REFERENT = 'Référente administrative';
    public const STERILIZATION_REFERENT = 'Référente stérilisation';

    public const ALL = [
        self::DENTIST,
        self::DENTAL_ASSISTANT,
        self::ADMIN_ASSISTANT,
        self::OPERATIONS_DIRECTOR,
        self::CLINICAL_REFERENT,
        self::ADMIN_REFERENT,
        self::STERILIZATION_REFERENT,
    ];

    public static function list(): array
    {
        return self::ALL;
    }
}
