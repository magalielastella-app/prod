<?php

namespace App\Support\ReviewTemplates;

/**
 * Trame d'entretien annuel — Assistant(e) administratif.
 *
 * Variantes par rapport à la trame « assistant dentaire » :
 *  - Entête : « Agendas gérés » à la place de « Praticien binôme ».
 *  - Section 01 : ergonomie du poste + outils de travail (au lieu de
 *    fauteuil + stérilisation), avec commentaires par bloc.
 *  - Section 03 : grille de compétences spécifique administrative (6 lignes).
 *  - Section 06 : ressenti vie pro / perso en champ libre.
 */
class AdminAssistantTemplate
{
    public const KEY = 'assistant_admin';

    public static function definition(): array
    {
        $evaluationOptions = ['Dépassé', 'Atteint', 'Partiellement atteint', 'Non réalisé'];

        return [
            'key' => self::KEY,
            'label' => "Assistant(e) administratif",
            'header' => [
                ['key' => 'agendas_geres', 'label' => 'Agendas gérés', 'owner' => 'manager'],
                ['key' => 'responsabilites', 'label' => 'Responsabilité au cabinet (missions complémentaires)', 'owner' => 'employee'],
                ['key' => 'qui_realise_entretien', 'label' => "Qui réalise l'entretien", 'owner' => 'manager'],
            ],
            'sections' => [
                [
                    'title' => '01. Bien-être & Qualité de Vie, Conditions de Travail',
                    'fields' => [
                        ['type' => 'scale_10', 'key' => 'bien_etre_travail', 'question' => 'Comment vous sentez-vous dans votre travail ?', 'owner' => 'employee'],
                        ['type' => 'scale_10', 'key' => 'bien_etre_poste', 'question' => 'Le poste que vous occupez vous plaît-il ?', 'owner' => 'employee'],
                        ['type' => 'scale_10', 'key' => 'bien_etre_relations', 'question' => "Les relations avec les autres membres de l'équipe sont-elles bonnes ?", 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'bien_etre_commentaires', 'question' => 'Commentaires', 'owner' => 'employee'],
                        ['type' => 'scale_10', 'key' => 'ergonomie_poste', 'question' => "Comment noteriez-vous l'ergonomie de votre poste de travail ?", 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'ergonomie_poste_commentaires', 'question' => 'Commentaires', 'owner' => 'employee'],
                        ['type' => 'scale_10', 'key' => 'outils_travail', 'question' => 'Comment noteriez-vous vos outils de travail (logiciels, matériels…) ?', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'outils_commentaires', 'question' => 'Commentaires', 'owner' => 'employee'],
                    ],
                ],
                [
                    'title' => "02. Bilan de l'année écoulée",
                    'fields' => [
                        [
                            'type' => 'objectives_review',
                            'key' => 'bilan_objectifs',
                            'question' => "Évaluation des objectifs de l'année écoulée",
                            'hint' => "Échelle d'évaluation : dépassé / atteint / partiellement atteint / non réalisé",
                            'evaluation_options' => $evaluationOptions,
                        ],
                        ['type' => 'textarea', 'key' => 'bilan_commentaires', 'question' => 'Commentaires', 'owner' => 'manager'],
                        [
                            'type' => 'activities_table',
                            'key' => 'activites',
                            'question' => 'Quelles sont les activités réalisées et faits marquants ?',
                            'owner' => 'employee',
                        ],
                    ],
                ],
                [
                    'title' => '03. Bilan des compétences attendues pour le poste occupé',
                    'fields' => [
                        ['type' => 'textarea', 'key' => 'reussites_poste', 'question' => 'Que réussissez-vous le mieux dans votre poste actuel ?', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'plaisir_fonctions', 'question' => "Qu'aimez-vous le plus dans vos fonctions actuelles ?", 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'axes_amelioration', 'question' => "Quels sont vos points d'amélioration ou compétences à acquérir ?", 'owner' => 'employee'],
                        [
                            'type' => 'competency_grid',
                            'key' => 'competences',
                            'question' => 'Bilan des compétences techniques, comportementales et relationnelles attendues',
                            'hint' => "Échelle d'évaluation : dépassé / atteint / partiellement atteint / non réalisé",
                            'evaluation_options' => $evaluationOptions,
                            'rows' => [
                                "Je maîtrise les compétences d'assistanat administratif",
                                'Je maîtrise les outils informatiques',
                                "J'accueille les patients chaleureusement afin de contribuer à leur bien-être",
                                'Je suis proactif(ve)',
                                'Je me positionne en solution',
                                "Je suis à l'aise avec la communication (patients, collaborateurs, praticiens)",
                            ],
                        ],
                        ['type' => 'textarea', 'key' => 'competences_non_utilisees', 'question' => "Avez-vous des compétences non utilisées susceptibles d'être mises à profit dans votre vie professionnelle ?", 'owner' => 'employee'],
                    ],
                ],
                [
                    'title' => "04. Perspectives pour l'année à venir",
                    'fields' => [
                        [
                            'type' => 'objectives_plan',
                            'key' => 'nouveaux_objectifs',
                            'question' => "Objectifs pour l'année à venir",
                            'owner' => 'manager',
                        ],
                    ],
                ],
                [
                    'title' => '05. Formations',
                    'fields' => [
                        ['type' => 'textarea', 'key' => 'formations_salarie', 'question' => 'Formations souhaitées par le salarié', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'formations_salarie_objectifs', 'question' => 'Objectifs visés (salarié)', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'formations_manager', 'question' => 'Formations souhaitées par le manager', 'owner' => 'manager'],
                        ['type' => 'textarea', 'key' => 'formations_manager_objectifs', 'question' => 'Objectifs visés (manager)', 'owner' => 'manager'],
                    ],
                ],
                [
                    'title' => "06. Conditions d'activité",
                    'fields' => [
                        ['type' => 'textarea', 'key' => 'conditions_remarques', 'question' => "Quelles sont vos remarques, points d'amélioration ou suggestions d'amélioration liés à vos conditions d'activité ?", 'owner' => 'employee'],
                        ['type' => 'scale_10', 'key' => 'charge_travail', 'question' => 'Comment évaluez-vous votre charge de travail ?', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'charge_commentaires', 'question' => 'Commentaires', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'vie_privee_pro', 'question' => "Quel est votre ressenti quant à l'articulation de votre vie privée / vie professionnelle ?", 'owner' => 'employee'],
                    ],
                ],
                [
                    'title' => "07. Synthèse de l'entretien annuel",
                    'fields' => [
                        ['type' => 'textarea', 'key' => 'synthese_collaborateur', 'question' => 'Commentaire du collaborateur', 'owner' => 'employee'],
                        ['type' => 'textarea', 'key' => 'synthese_manager', 'question' => 'Commentaire du manager', 'owner' => 'manager'],
                    ],
                ],
            ],
        ];
    }
}
