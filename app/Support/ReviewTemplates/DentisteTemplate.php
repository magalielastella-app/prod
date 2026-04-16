<?php

namespace App\Support\ReviewTemplates;

/**
 * Trame d'entretien annuel — Dentiste (praticien).
 *
 * Structure simple basée sur des questions ouvertes (textarea) — adaptée
 * aux entretiens de pairs (un dentiste évalue un autre dentiste).
 *
 * NB : la section 1 n'a pas encore été communiquée par le cabinet ;
 *      elle sera ajoutée ici quand son contenu sera connu.
 */
class DentisteTemplate
{
    public const KEY = 'dentiste';

    public static function definition(): array
    {
        return [
            'key' => self::KEY,
            'label' => 'Dentiste (praticien)',
            'header' => [
                // Entête minimal — l'entretien est conduit par un pair désigné par la directrice.
                ['key' => 'praticien_evaluateur', 'label' => "Praticien qui réalise l'entretien", 'owner' => 'manager'],
            ],
            'sections' => [
                [
                    'title' => '01. [à compléter]',
                    'fields' => [
                        // Contenu de la section 1 non encore fourni — placeholder provisoire.
                        ['type' => 'textarea', 'key' => 'section1_placeholder', 'question' => '(Section en attente — merci de fournir le contenu.)', 'owner' => 'employee'],
                    ],
                ],
                [
                    'title' => '02. Activité professionnelle & organisation',
                    'fields' => [
                        ['type' => 'textarea', 'key' => 'bilan_annee', 'question' => "Bilan de l'année écoulée : quels sont vos réussites et points forts cette année dans votre pratique ?", 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'difficultes', 'question' => 'Difficultés rencontrées : quels obstacles ou sources de frustration avez-vous rencontrés dans votre activité ?', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'gestion_temps', 'question' => 'Gestion du temps et des rendez-vous : êtes-vous satisfait(e) de votre rythme de travail et de la planification des consultations ?', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'collaboration_equipe', 'question' => 'Collaboration en équipe : comment percevez-vous la communication et la coopération avec les assistant(e)s, secrétaires, assistante de direction ?', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'collaboration_dentistes', 'question' => 'Collaboration en équipe : comment percevez-vous la communication et la coopération avec vos collègues dentistes ?', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'section2_commentaire', 'question' => 'Commentaire libre', 'owner' => 'employee'],
                    ],
                ],
                [
                    'title' => '03. Développement professionnel',
                    'fields' => [
                        ['type' => 'textarea', 'key' => 'competences_renforcer', 'question' => "Compétences à renforcer : y a-t-il des techniques ou connaissances que vous aimeriez approfondir ou développer ?", 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'formations', 'question' => "Formations et spécialisations : souhaitez-vous suivre des formations spécifiques dans l'année à venir ?", 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'innovation_materiel', 'question' => "Innovation et matériel : avez-vous des besoins ou envies concernant de nouveaux équipements ou outils qui pourraient améliorer votre pratique ?", 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'section3_commentaire', 'question' => 'Commentaire libre', 'owner' => 'employee'],
                    ],
                ],
                [
                    'title' => '04. Relation patient',
                    'fields' => [
                        ['type' => 'textarea', 'key' => 'satisfaction_patients', 'question' => 'Satisfaction des patients : selon vous, comment les patients perçoivent-ils la qualité de vos soins et de votre accompagnement ?', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'experience_patient', 'question' => "Expérience patient : quelles améliorations pourraient être mises en place pour optimiser l'accueil, le suivi ou la relation avec les patients ?", 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'section4_commentaire', 'question' => 'Commentaire libre', 'owner' => 'employee'],
                    ],
                ],
                [
                    'title' => '05. Bien-être & équilibre personnel',
                    'fields' => [
                        ['type' => 'textarea', 'key' => 'equilibre_pro_perso', 'question' => 'Équilibre vie pro / vie perso : arrivez-vous à préserver un équilibre satisfaisant entre travail et vie privée ?', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'sante_bien_etre', 'question' => "Santé et bien-être : qu'est-ce qui vous aide à gérer le stress lié à votre métier ? Auriez-vous besoin de soutien supplémentaire ?", 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'motivation_perspectives', 'question' => "Motivation & perspectives : qu'est-ce qui vous motive le plus dans votre métier aujourd'hui ? Et quelles sont vos attentes pour l'avenir dans le cabinet ?", 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'section5_commentaire', 'question' => 'Commentaire libre', 'owner' => 'employee'],
                    ],
                ],
                [
                    // Section additionnelle : permet au dentiste évaluateur (pair) de
                    // poser ses observations et recommandations écrites.
                    'title' => "Synthèse — commentaire du dentiste évaluateur",
                    'fields' => [
                        ['type' => 'textarea', 'key' => 'synthese_manager', 'question' => "Observations, préconisations et points d'attention pour l'année à venir", 'owner' => 'manager'],
                    ],
                ],
            ],
        ];
    }
}
