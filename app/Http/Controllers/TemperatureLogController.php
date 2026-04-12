<?php

namespace App\Http\Controllers;

use App\Models\TemperatureLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TemperatureLogController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'date' => ['required', 'date'],
            'zone' => ['required', 'string'],
            'temp' => ['required', 'numeric'],
            'time' => ['required', 'date_format:H:i'],
            'agent' => ['required', 'string', 'max:255'],
        ]);
        $data['compliant'] = TemperatureLog::isCompliant($data['zone'], (float) $data['temp']);
        TemperatureLog::create($data);

        $msg = $data['compliant'] ? 'Relevé enregistré' : "⚠ Température hors seuil pour {$data['zone']}";
        return back()->with($data['compliant'] ? 'success' : 'error', $msg);
    }

    public function destroy(TemperatureLog $temperatureLog): RedirectResponse
    {
        $temperatureLog->delete();
        return back()->with('success', 'Relevé supprimé');
    }
}
