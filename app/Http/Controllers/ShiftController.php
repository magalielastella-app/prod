<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ShiftController extends Controller
{
    /** Affiche la grille planning pour la semaine ciblée (?week=YYYY-MM-DD, lundi). */
    public function index(Request $request): Response
    {
        $mondayParam = $request->string('week')->value();
        $monday = $mondayParam
            ? Carbon::parse($mondayParam)->startOfWeek()
            : Carbon::now()->startOfWeek();
        $sunday = (clone $monday)->endOfWeek();

        $employees = Employee::orderBy('name')->get();
        $shifts = Shift::whereBetween('date', [$monday, $sunday])
            ->orderBy('start')
            ->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'employee_id' => $s->employee_id,
                'date' => $s->date->toDateString(),
                'start' => substr($s->start, 0, 5),
                'end' => substr($s->end, 0, 5),
                'role' => $s->role,
            ]);

        return Inertia::render('Planning/Index', [
            'employees' => $employees,
            'shifts' => $shifts,
            'weekStart' => $monday->toDateString(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        if ($data['end'] <= $data['start']) {
            return back()->withErrors(['end' => 'L\'heure de fin doit être après le début']);
        }
        Shift::create($data);
        return back()->with('success', 'Créneau ajouté');
    }

    public function destroy(Shift $shift): RedirectResponse
    {
        $shift->delete();
        return back()->with('success', 'Créneau supprimé');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'date' => ['required', 'date'],
            'start' => ['required', 'date_format:H:i'],
            'end' => ['required', 'date_format:H:i'],
            'role' => ['nullable', 'string', 'max:100'],
        ]);
    }
}
