@php
    $employeeAnswers = $review->employee_answers ?? [];
    $managerAnswers = $review->manager_answers ?? [];
    $header = $review->header ?? [];

    $fieldAnswers = function (array $field) use ($employeeAnswers, $managerAnswers) {
        $owner = $field['owner'] ?? null;
        if ($owner === 'manager') return $managerAnswers[$field['key']] ?? null;
        return $employeeAnswers[$field['key']] ?? null;
    };
    $fmt = fn ($d) => $d ? \Illuminate\Support\Carbon::parse($d)->format('d/m/Y') : '—';
    $fmtDT = fn ($d) => $d ? \Illuminate\Support\Carbon::parse($d)->format('d/m/Y \à H:i') : null;
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Entretien {{ $review->year }} — {{ $review->employee->name }}</title>
    <style>
        @page { size: A4; margin: 1.2cm 1.4cm; }
        * { box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9.5pt;
            color: #0F172A;
            margin: 0;
            padding: 0;
        }

        /* Bande logo / entête */
        table.hero {
            width: 100%;
            background: #0F4C47;
            color: #FFFFFF;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        table.hero td {
            padding: 12px 14px;
            vertical-align: middle;
            border: 0;
        }
        .logo-mark {
            display: inline-block;
            background: #FFFFFF;
            color: #0F4C47;
            padding: 8px 10px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 8.5pt;
            line-height: 1.05;
            letter-spacing: 0.05em;
            white-space: pre;
        }
        .hero-title { font-size: 14pt; font-weight: bold; }
        .hero-sub { font-size: 10pt; }

        /* Table d'infos */
        table.info { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        table.info th, table.info td {
            border: 1px solid #CBD5E1;
            padding: 5px 7px;
            font-size: 9pt;
            text-align: left;
            vertical-align: top;
        }
        table.info th { background: #F1F5F9; font-weight: bold; width: 22%; }

        /* Sections */
        h2.section-title {
            font-size: 11pt;
            color: #FFFFFF;
            background: #115E59;
            padding: 6px 10px;
            margin: 16px 0 8px;
            border-radius: 3px;
        }

        /* Champs */
        .field { margin: 8px 0; page-break-inside: avoid; }
        .field .label {
            font-size: 9.5pt;
            font-weight: bold;
            color: #0F172A;
            background: #FEF3C7;
            padding: 4px 7px;
            border-radius: 2px;
        }
        .field .hint { font-size: 8pt; color: #64748B; font-style: italic; margin-top: 2px; }
        .field .value {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            padding: 5px 7px;
            margin-top: 4px;
            white-space: pre-wrap;
            min-height: 18px;
            font-size: 9.5pt;
        }
        .empty { color: #94A3B8; font-style: italic; }

        /* Échelle 1..10 — rendu via table pour dompdf */
        table.scale { width: 100%; border-collapse: collapse; margin-top: 4px; }
        table.scale td {
            text-align: center;
            border: 1px solid #CBD5E1;
            padding: 5px 0;
            font-size: 9pt;
            font-weight: bold;
            background: #FFFFFF;
            width: 10%;
        }
        table.scale td.selected {
            background: #FDE047;
            border-color: #F59E0B;
        }

        /* Tables de données */
        table.grid {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
            font-size: 9pt;
        }
        table.grid th, table.grid td {
            border: 1px solid #CBD5E1;
            padding: 5px 7px;
            vertical-align: top;
        }
        table.grid th { background: #F1F5F9; font-weight: bold; text-align: left; }
        table.grid td.row-label { background: #FEF3C7; font-weight: bold; width: 28%; }

        /* Signatures */
        table.signatures { width: 100%; border-collapse: separate; border-spacing: 10px 0; margin-top: 18px; }
        table.signatures td {
            width: 50%;
            border: 1px solid #CBD5E1;
            border-radius: 6px;
            padding: 12px;
            vertical-align: top;
        }
        .sig-who { font-weight: bold; color: #115E59; margin-bottom: 4px; font-size: 10pt; }
        .sig-signed { color: #047857; font-weight: bold; margin-top: 8px; }
        .sig-unsigned { color: #94A3B8; font-style: italic; margin-top: 8px; }
    </style>
</head>
<body>

<table class="hero">
    <tr>
        <td style="width: 130px;">
            <span class="logo-mark">CABINET
DENTAIRE
DE L'OBIOU</span>
        </td>
        <td>
            <div class="hero-title">Entretien annuel {{ $review->year }}</div>
            <div class="hero-sub">{{ $review->employee->name }} — {{ $review->employee->position ?? '—' }}</div>
        </td>
    </tr>
</table>

<table class="info">
    <tr>
        <th>Nom complet</th>
        <td>{{ $review->employee->name }}</td>
        <th>Date d'embauche</th>
        <td>{{ $fmt($review->employee->hired_on) }}</td>
    </tr>
    <tr>
        <th>Poste occupé</th>
        <td>{{ $review->employee->position ?? '—' }}</td>
        <th>Date de l'entretien</th>
        <td>{{ $fmt($review->scheduled_for) }}</td>
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
                <table class="scale">
                    <tr>
                        @for ($i = 1; $i <= 10; $i++)
                            <td @if ((int) $value === $i) class="selected" @endif>{{ $i }}</td>
                        @endfor
                    </tr>
                </table>
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
                            <th>Indicateurs</th>
                            <th>Moyens</th>
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
                            $empCells = is_array($employeeAnswers[$field['key']] ?? null) ? $employeeAnswers[$field['key']] : [];
                            $mgrCells = is_array($managerAnswers[$field['key']] ?? null) ? $managerAnswers[$field['key']] : [];
                        @endphp
                        @foreach ($field['rows'] ?? [] as $i => $rowLabel)
                            @php
                                $autoVal = is_array($empCells[$i] ?? null) ? ($empCells[$i]['auto'] ?? '') : ($empCells[$i] ?? '');
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

<h2 class="section-title">Signatures</h2>
<table class="signatures">
    <tr>
        <td>
            <div class="sig-who">Salarié</div>
            <div>{{ $review->employee->name }}</div>
            @if ($review->employee_signed_at)
                <div class="sig-signed">Signé le {{ $fmtDT($review->employee_signed_at) }}</div>
            @else
                <div class="sig-unsigned">Non signé</div>
            @endif
        </td>
        <td>
            <div class="sig-who">Qui réalise l'entretien</div>
            <div>{{ $review->manager?->name ?? '—' }}</div>
            @if ($review->manager_signed_at)
                <div class="sig-signed">Signé le {{ $fmtDT($review->manager_signed_at) }}</div>
            @else
                <div class="sig-unsigned">Non signé</div>
            @endif
        </td>
    </tr>
    @if ($review->coManager)
        <tr>
            <td colspan="2" style="padding-top: 10px;">
                <div class="sig-who">Qui assiste à l'entretien</div>
                <div>{{ $review->coManager->name }}</div>
                <div style="font-size: 8pt; color: #64748B; margin-top: 2px;">(présence pour trace, ne signe pas)</div>
            </td>
        </tr>
    @endif
</table>

</body>
</html>
