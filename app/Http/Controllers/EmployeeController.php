<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        Employee::create($this->validated($request));
        return back()->with('success', 'Employé ajouté');
    }

    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $employee->update($this->validated($request));
        return back()->with('success', 'Employé mis à jour');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        $employee->delete(); // cascade sur les shifts via FK
        return back()->with('success', 'Employé supprimé');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);
    }
}
