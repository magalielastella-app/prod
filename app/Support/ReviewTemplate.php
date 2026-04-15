<?php

namespace App\Support;

use App\Support\ReviewTemplates\AdminAssistantTemplate;
use App\Support\ReviewTemplates\AssistantTemplate;
use App\Support\ReviewTemplates\DirectriceTemplate;

/**
 * Registre des trames d'entretien annuel.
 *
 * Le poste « Dentiste » utilise pour l'instant la trame « assistant »
 * par défaut jusqu'à ce qu'une trame dédiée soit fournie.
 */
class ReviewTemplate
{
    public const DEFAULT = AssistantTemplate::KEY;

    /**
     * Renvoie la définition d'une trame à partir de sa clé.
     */
    public static function get(string $key): array
    {
        return match ($key) {
            AssistantTemplate::KEY => AssistantTemplate::definition(),
            AdminAssistantTemplate::KEY => AdminAssistantTemplate::definition(),
            DirectriceTemplate::KEY => DirectriceTemplate::definition(),
            default => AssistantTemplate::definition(),
        };
    }

    /**
     * Détermine la clé de trame en fonction du poste.
     */
    public static function keyForPosition(?string $position): string
    {
        return match ($position) {
            Positions::DENTAL_ASSISTANT => AssistantTemplate::KEY,
            Positions::ADMIN_ASSISTANT => AdminAssistantTemplate::KEY,
            Positions::OPERATIONS_DIRECTOR => DirectriceTemplate::KEY,
            default => self::DEFAULT,
        };
    }

    /**
     * Récupère la liste de toutes les clés de champs « employee » d'une trame.
     */
    public static function employeeFieldKeys(array $definition): array
    {
        return self::fieldKeysForOwner($definition, 'employee');
    }

    public static function managerFieldKeys(array $definition): array
    {
        return self::fieldKeysForOwner($definition, 'manager');
    }

    private static function fieldKeysForOwner(array $definition, string $owner): array
    {
        $keys = [];
        // Header
        foreach ($definition['header'] ?? [] as $headerField) {
            if (($headerField['owner'] ?? null) === $owner) {
                $keys[] = $headerField['key'];
            }
        }
        // Sections
        foreach ($definition['sections'] ?? [] as $section) {
            foreach ($section['fields'] ?? [] as $field) {
                $fieldOwner = $field['owner'] ?? self::implicitOwner($field['type'] ?? '');
                if ($fieldOwner === $owner) {
                    $keys[] = $field['key'];
                }
            }
        }
        return $keys;
    }

    /**
     * Pour les widgets composites, le owner est implicite :
     *  - objectives_review : rempli par le manager
     *  - competency_grid   : les deux parties (stocké séparément)
     * On renvoie null pour signaler qu'il faut traiter au cas par cas.
     */
    private static function implicitOwner(string $type): ?string
    {
        return match ($type) {
            'objectives_review', 'objectives_plan' => 'manager',
            default => null,
        };
    }
}
