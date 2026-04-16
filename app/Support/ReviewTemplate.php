<?php

namespace App\Support;

use App\Models\ReviewTemplateModel;
use App\Support\ReviewTemplates\AdminAssistantTemplate;
use App\Support\ReviewTemplates\AssistantTemplate;
use App\Support\ReviewTemplates\DentisteTemplate;
use App\Support\ReviewTemplates\DirectriceTemplate;

/**
 * Registre des trames d'entretien annuel.
 *
 * Résolution : la base de données (table review_templates) est
 * consultée en priorité ; si la trame n'existe pas encore en base,
 * on retombe sur la définition PHP statique.
 */
class ReviewTemplate
{
    public const DEFAULT = AssistantTemplate::KEY;

    /**
     * Renvoie la définition d'une trame à partir de sa clé.
     * DB d'abord, fallback PHP ensuite.
     */
    public static function get(string $key): array
    {
        $db = ReviewTemplateModel::where('key', $key)->first();
        if ($db) {
            return $db->toDefinition();
        }

        return self::getFromPhp($key);
    }

    /**
     * Récupère toutes les trames connues (DB puis complétées par le PHP).
     */
    public static function all(): array
    {
        $dbTemplates = ReviewTemplateModel::all()->keyBy('key');
        $phpKeys = [
            AssistantTemplate::KEY,
            AdminAssistantTemplate::KEY,
            DirectriceTemplate::KEY,
            DentisteTemplate::KEY,
        ];

        $result = [];
        foreach ($phpKeys as $k) {
            if ($dbTemplates->has($k)) {
                $result[] = $dbTemplates->get($k)->toDefinition();
            } else {
                $result[] = self::getFromPhp($k);
            }
        }
        // Ajouter les trames DB-only (créées via l'UI, pas en PHP)
        foreach ($dbTemplates as $k => $model) {
            if (! in_array($k, $phpKeys, true)) {
                $result[] = $model->toDefinition();
            }
        }
        return $result;
    }

    private static function getFromPhp(string $key): array
    {
        return match ($key) {
            AssistantTemplate::KEY => AssistantTemplate::definition(),
            AdminAssistantTemplate::KEY => AdminAssistantTemplate::definition(),
            DirectriceTemplate::KEY => DirectriceTemplate::definition(),
            DentisteTemplate::KEY => DentisteTemplate::definition(),
            default => AssistantTemplate::definition(),
        };
    }

    /**
     * Détermine la clé de trame en fonction du poste.
     */
    public static function keyForPosition(?string $position): string
    {
        return match ($position) {
            Positions::DENTAL_ASSISTANT,
            Positions::CLINICAL_REFERENT,
            Positions::STERILIZATION_REFERENT => AssistantTemplate::KEY,

            Positions::ADMIN_ASSISTANT,
            Positions::ADMIN_REFERENT => AdminAssistantTemplate::KEY,

            Positions::OPERATIONS_DIRECTOR => DirectriceTemplate::KEY,
            Positions::DENTIST => DentisteTemplate::KEY,
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
