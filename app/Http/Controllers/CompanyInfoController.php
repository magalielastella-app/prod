<?php

namespace App\Http\Controllers;

use App\Models\CompanyInfo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CompanyInfoController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'legal_form' => ['nullable', 'string', 'max:100'],
            'siret' => ['nullable', 'string', 'max:20'],
            'vat_number' => ['nullable', 'string', 'max:30'],
            'rcs' => ['nullable', 'string', 'max:100'],
            'ape_code' => ['nullable', 'string', 'max:10'],
            'capital' => ['nullable', 'numeric', 'min:0'],
            'address' => ['nullable', 'string', 'max:500'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'city' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'manager_name' => ['nullable', 'string', 'max:255'],
            'opening_hours' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        CompanyInfo::current()->update($data);
        return back()->with('success', 'Informations société mises à jour');
    }
}
