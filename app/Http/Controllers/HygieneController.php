<?php

namespace App\Http\Controllers;

use App\Models\CleaningTask;
use App\Models\Delivery;
use App\Models\TemperatureLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HygieneController extends Controller
{
    public function index(Request $request): Response
    {
        $date = $request->string('date')->value() ?: Carbon::today()->toDateString();

        return Inertia::render('Hygiene/Index', [
            'date' => $date,
            'zones' => array_keys(TemperatureLog::ZONE_LIMITS),
            'frequencies' => CleaningTask::FREQUENCIES,
            'temperatures' => TemperatureLog::whereDate('date', $date)
                ->orderBy('time')->get(),
            'cleaningTasks' => CleaningTask::orderBy('zone')->get(),
            'deliveries' => Delivery::whereDate('date', $date)
                ->orderBy('supplier')->get(),
        ]);
    }
}
