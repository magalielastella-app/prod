@php
    /**
     * Vue imprimable d'un entretien annuel.
     * L'utilisateur utilise Ctrl/Cmd+P et "Enregistrer au format PDF".
     */
    $employeeAnswers = $review->employee_answers ?? [];
    $managerAnswers = $review->manager_answers ?? [];
    $header = $review->header ?? [];

    $fieldAnswers = function (array $field) use ($employeeAnswers, $managerAnswers) {
        $owner = $field['owner'] ?? null;
        if ($owner === 'manager') return $managerAnswers[$field['key']] ?? null;
        return $employeeAnswers[$field['key']] ?? null;
    };
    $formatDate = fn ($d) => $d ? \Illuminate\Support\Carbon::parse($d)->format('d/m/Y') : '—';
    $formatDateTime = fn ($d) => $d ? \Illuminate\Support\Carbon::parse($d)->format('d/m/Y \à H:i') : null;

    $filename = sprintf(
        '%s_%s_Entretien_%d',
        $review->year,
        \Illuminate\Support\Str::upper(\Illuminate\Support\Str::slug($review->employee->name, '_')),
        $review->year
    );
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>{{ $filename }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        @page { size: A4; margin: 1.2cm 1.4cm; }
        * { box-sizing: border-box; }
        html, body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10pt;
            color: #0F172A;
            margin: 0;
            padding: 0;
            background: #fff;
        }
        a { color: inherit; text-decoration: none; }

        /* Barre d'action en haut (cachée à l'impression) */
        .toolbar {
            background: #ECFEFF;
            border-bottom: 2px solid #14B8A6;
            padding: 12px 20px;
            display: flex;
            gap: 12px;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
        }
        .toolbar button, .toolbar a {
            background: #115E59;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
        }
        .toolbar a.ghost { background: transparent; color: #115E59; border: 1px solid #14B8A6; }
        .toolbar .hint { font-size: 11px; color: #475569; width: 100%; text-align: center; margin-top: 4px; }

        /* Conteneur principal */
        .page { max-width: 780px; margin: 0 auto; padding: 20px; }

        /* En-tête avec logo */
        .logo-header {
            background: #0F4C47;
            color: white;
            padding: 14px 18px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 14px;
        }
        .logo-mark {
            background: white;
            color: #0F4C47;
            padding: 8px 12px;
            border-radius: 4px;
            font-weight: 900;
            font-size: 9pt;
            line-height: 1.05;
            letter-spacing: 0.05em;
            white-space: pre;
        }
        .logo-title { font-size: 14pt; font-weight: 700; margin: 0; }
        .logo-sub { font-size: 10pt; opacity: 0.9; margin-top: 2px; }

        /* Infos entête */
        table.info { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        table.info th, table.info td {
            border: 1px solid #CBD5E1;
            padding: 5px 8px;
            font-size: 9.5pt;
            text-align: left;
            vertical-align: top;
        }
        table.info th { background: #F1F5F9; font-weight: 600; width: 22%; }

        /* Sections */
        h2.section-title {
            font-size: 12pt;
            color: #FFFFFF;
            background: #115E59;
            padding: 7px 12px;
            border-radius: 4px;
            margin: 20px 0 10px;
        }

        /* Champ */
        .field { margin: 10px 0; page-break-inside: avoid; }
        .field .label {
            font-size: 10pt;
            font-weight: 600;
            color: #0F172A;
            background: #FEF3C7;
            padding: 4px 8px;
            border-radius: 3px;
        }
        .field .hint { font-size: 8.5pt; color: #64748B; margin-top: 2px; font-style: italic; }
        .field .value {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            padding: 6px 8px;
            border-radius: 3px;
            margin-top: 4px;
            white-space: pre-wrap;
            min-height: 20px;
        }
        .empty { color: #94A3B8; font-style: italic; }

        /* Échelle 1..10 */
        .scale {
            display: flex;
            gap: 2px;
            margin-top: 6px;
        }
        .scale-val {
            flex: 1;
            text-align: center;
            border: 1px solid #CBD5E1;
            padding: 5px 0;
            font-size: 9pt;
            font-weight: 600;
            background: #fff;
        }
        .scale-val.selected {
            background: #FDE047;
            border-color: #F59E0B;
            color: #0F172A;
        }

        /* Tables */
        table.grid {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            font-size: 9.5pt;
        }
        table.grid th, table.grid td {
            border: 1px solid #CBD5E1;
            padding: 6px 8px;
            vertical-align: top;
        }
        table.grid th { background: #F1F5F9; font-weight: 600; }
        table.grid td.row-label { background: #FEF3C7; font-weight: 600; width: 28%; }

        /* Signatures */
        .signatures {
            margin-top: 30px;
            display: flex;
            gap: 16px;
            page-break-inside: avoid;
        }
        .signature-box {
            flex: 1;
            border: 1px solid #CBD5E1;
            border-radius: 6px;
            padding: 14px;
            min-height: 80px;
        }
        .signature-box .who { font-weight: 700; color: #115E59; margin-bottom: 4px; }
        .signature-box .name { font-size: 10pt; }
        .signature-box .signed { color: #047857; font-weight: 600; margin-top: 10px; }
        .signature-box .unsigned { color: #94A3B8; font-style: italic; margin-top: 10px; }

        @media print {
            .toolbar { display: none !important; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .page { padding: 0; max-width: none; }
        }
    </style>
</head>
<body>

<div class="toolbar">
    <button onclick="window.print()">📄 Enregistrer en PDF</button>
    <a href="{{ route('reviews.show', $review->id) }}" class="ghost">← Retour</a>
    <div class="hint">
        Une boîte de dialogue va s'ouvrir — choisissez <strong>Enregistrer au format PDF</strong>
        comme destination.
    </div>
</div>

<div class="page">

    <div class="logo-header">
        <div class="logo-mark">CABINET
DENTAIRE
DE L'OBIOU</div>
        <div>
            <div class="logo-title">Entretien annuel {{ $review->year }}</div>
            <div class="logo-sub">{{ $review->employee->name }} — {{ $review->employee->position ?? '—' }}</div>
        </div>
    </div>

    <table class="info">
        <tr>
            <th>Nom complet</th>
            <td>{{ $review->employee->name }}</td>
            <th>Date d'embauche</th>
            <td>{{ $formatDate($review->employee->hired_on) }}</td>
        </tr>
        <tr>
            <th>Poste occupé</th>
            <td>{{ $review->employee->position ?? '—' }}</td>
            <th>Date de l'entretien</th>
            <td>{{ $formatDate($review->scheduled_for) }}</td>
        </tr>
        <tr>
            <th>Qui réalise l'entretien</th>
            <td colspan="3">{{ $review->manager?->name ?? '—' }}</td>
        </tr>
        @if ($review->coManager)
            <tr>
                <th>Qui assiste à l'entretien</th>
                <td colspan="3">{{ $review->coManager->name }}</td>
            </tr>
        @endif
        @foreach ($template['header'] ?? [] as $headerField)
            <tr>
                <th>{{ $headerField['label'] }}</th>
                <td colspan="3">{{ $header[$headerField['key']] ?? '' ?: '—' }}</td>
            </tr>
        @endforeach
    </table>

    @foreach ($template['sections'] as $section)
        <h2 class="section-title">{{ $section['title'] }}</h2>

        @foreach ($section['fields'] as $field)
            @php
                $type = $field['type'] ?? '';
                $value = $fieldAnswers($field);
            @endphp

            @if ($type === 'scale_10')
                <div class="field">
                    <div class="label">{{ $field['question'] }}</div>
                    <div class="scale">
                        @for ($i = 1; $i <= 10; $i++)
                            <div class="scale-val @if ((int) $value === $i) selected @endif">{{ $i }}</div>
                        @endfor
                    </div>
                </div>

            @elseif ($type === 'text' || $type === 'textarea' || $type === 'choice')
                <div class="field">
                    <div class="label">{{ $field['question'] }}</div>
                    <div class="value">{{ $value ?: '—' }}</div>
                </div>

            @elseif ($type === 'objectives_review')
                <div class="field">
                    <div class="label">{{ $field['question'] }}</div>
                    @if (!empty($field['hint']))
                        <div class="hint">{{ $field['hint'] }}</div>
                    @endif
                    <table class="grid">
                        <thead>
                            <tr>
                                <th style="width: 60%;">Objectif fixé l'an passé</th>
                                <th>Évaluation manager</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $rows = $managerAnswers[$field['key']] ?? []; @endphp
                            @forelse ($rows as $row)
                                <tr>
                                    <td>{{ is_array($row) ? ($row['objectif'] ?? '') : '' }}</td>
                                    <td>{{ is_array($row) ? ($row['evaluation'] ?? '') : '' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="empty">— aucun objectif renseigné —</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            @elseif ($type === 'activities_table')
                <div class="field">
                    <div class="label">{{ $field['question'] }}</div>
                    <table class="grid">
                        <thead>
                            <tr>
                                <th>Réalisations</th>
                                <th>Réussites</th>
                                <th>Difficultés</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $rows = $employeeAnswers[$field['key']] ?? []; @endphp
                            @forelse ($rows as $row)
                                <tr>
                                    <td>{{ is_array($row) ? ($row['realisations'] ?? '') : '' }}</td>
                                    <td>{{ is_array($row) ? ($row['reussites'] ?? '') : '' }}</td>
                                    <td>{{ is_array($row) ? ($row['difficultes'] ?? '') : '' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="empty">— aucune activité renseignée —</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            @elseif ($type === 'objectives_plan')
                <div class="field">
                    <div class="label">{{ $field['question'] }}</div>
                    <table class="grid">
                        <thead>
                            <tr>
                                <th>Objectif</th>
                                <th>Indicateurs de réalisation</th>
                                <th>Moyens à mettre en œuvre</th>
                                <th>Délais</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $rows = $managerAnswers[$field['key']] ?? []; @endphp
                            @forelse ($rows as $row)
                                <tr>
                                    <td>{{ is_array($row) ? ($row['objectif'] ?? '') : '' }}</td>
                                    <td>{{ is_array($row) ? ($row['indicateurs'] ?? '') : '' }}</td>
                                    <td>{{ is_array($row) ? ($row['moyens'] ?? '') : '' }}</td>
                                    <td>{{ is_array($row) ? ($row['delais'] ?? '') : '' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="empty">— aucun objectif renseigné —</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            @elseif ($type === 'competency_grid')
                <div class="field">
                    <div class="label">{{ $field['question'] }}</div>
                    @if (!empty($field['hint']))
                        <div class="hint">{{ $field['hint'] }}</div>
                    @endif
                    <table class="grid">
                        <thead>
                            <tr>
                                <th style="width: 28%;">Compétence</th>
                                <th>Auto-évaluation</th>
                                <th>Commentaires manager</th>
                                <th>Actions à mener</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $empCells = is_array($employeeAnswers[$field['key']] ?? null)
                                    ? $employeeAnswers[$field['key']]
                                    : [];
                                $mgrCells = is_array($managerAnswers[$field['key']] ?? null)
                                    ? $managerAnswers[$field['key']]
                                    : [];
                            @endphp
                            @foreach ($field['rows'] ?? [] as $i => $rowLabel)
                                @php
                                    $autoVal = is_array($empCells[$i] ?? null)
                                        ? ($empCells[$i]['auto'] ?? '')
                                        : ($empCells[$i] ?? '');
                                    $mgrObj = is_array($mgrCells[$i] ?? null) ? $mgrCells[$i] : [];
                                @endphp
                                <tr>
                                    <td class="row-label">{{ $rowLabel }}</td>
                                    <td>{{ $autoVal }}</td>
                                    <td>{{ $mgrObj['manager_comment'] ?? $mgrObj['commentaire'] ?? '' }}</td>
                                    <td>{{ $mgrObj['action'] ?? '' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            @endif
        @endforeach
    @endforeach

    <!-- Signatures -->
    <h2 class="section-title">Signatures</h2>
    <div class="signatures">
        <div class="signature-box">
            <div class="who">Salarié</div>
            <div class="name">{{ $review->employee->name }}</div>
            @if ($review->employee_signed_at)
                <div class="signed">✓ Signé le {{ $formatDateTime($review->employee_signed_at) }}</div>
            @else
                <div class="unsigned">Non signé</div>
            @endif
        </div>
        <div class="signature-box">
            <div class="who">Qui réalise l'entretien</div>
            <div class="name">{{ $review->manager?->name ?? '—' }}</div>
            @if ($review->manager_signed_at)
                <div class="signed">✓ Signé le {{ $formatDateTime($review->manager_signed_at) }}</div>
            @else
                <div class="unsigned">Non signé</div>
            @endif
        </div>
    </div>

    @if ($review->coManager)
        <div style="margin-top: 14px; padding: 12px; border: 1px dashed #CBD5E1; border-radius: 6px; background: #F8FAFC;">
            <div style="font-weight: 700; color: #115E59; margin-bottom: 2px;">Qui assiste à l'entretien</div>
            <div>{{ $review->coManager->name }}</div>
            <div style="font-size: 11px; color: #64748B; margin-top: 4px; font-style: italic;">
                Présence pour trace — ne signe pas l'entretien.
            </div>
        </div>
    @endif

</div>

<script>
    // Suggère un nom de fichier propre dans la boîte d'impression
    document.title = @json($filename);
</script>
</body>
</html>
