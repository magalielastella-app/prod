<?php

namespace App\Http\Controllers;

use App\Models\CleaningTask;
use App\Models\Delivery;
use App\Models\Employee;
use App\Models\Product;
use App\Models\Shift;
use App\Models\TemperatureLog;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();

        $products = Product::all();
        $lowStock = $products->filter(fn ($p) => in_array($p->status['label'], ['Stock bas', 'Rupture']));
        $expiring = $products->filter(fn ($p) =>
            $p->expiration && $p->expiration->diffInDays($today, false) >= -3
        );

        $shifts = Shift::whereBetween('date', [$weekStart, $weekEnd])->get();
        $weeklyHours = round($shifts->sum(fn ($s) => $s->hours), 1);

        $nonCompliantTemps = TemperatureLog::whereDate('date', $today)
            ->where('compliant', false)->get();

        $overdueTasks = CleaningTask::all()
            ->filter(fn ($t) => $t->status['cls'] !== 'ok')
            ->take(10)
            ->values();

        $alerts = [];
        foreach ($lowStock as $p) {
            $alerts[] = [
                'label' => "Stock bas : {$p->name} ({$p->quantity} {$p->unit})",
                'type' => 'warn',
            ];
        }
        foreach ($expiring as $p) {
            $days = $today->diffInDays($p->expiration, false);
            $alerts[] = [
                'label' => $days < 0
                    ? "Périmé : {$p->name} (" . $p->expiration->format('d/m/Y') . ')'
                    : "Péremption dans {$days}j : {$p->name}",
                'type' => $days < 0 ? 'danger' : 'warn',
            ];
        }
        foreach ($nonCompliantTemps as $t) {
            $alerts[] = [
                'label' => "Température non conforme : {$t->zone} ({$t->temp}°C)",
                'type' => 'danger',
            ];
        }

        return Inertia::render('Dashboard', [
            'stats' => [
                'products' => $products->count(),
                'lowStock' => $lowStock->count(),
                'expiring' => $expiring->count(),
                'employees' => Employee::count(),
                'weeklyHours' => $weeklyHours,
                'hygieneChecks' => TemperatureLog::whereDate('date', $today)->count()
                    + Delivery::whereDate('date', $today)->count(),
            ],
            'alerts' => array_slice($alerts, 0, 10),
            'pendingTasks' => $overdueTasks,
        ]);
    }
}
