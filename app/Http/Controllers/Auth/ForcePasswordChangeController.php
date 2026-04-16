<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class ForcePasswordChangeController extends Controller
{
    public function show(): Response
    {
        return Inertia::render('Auth/ForcePasswordChange');
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = $request->user();
        $user->update([
            'password' => Hash::make($request->password),
            'must_change_password' => false,
        ]);

        return redirect()->route('dashboard')->with('success', 'Mot de passe mis à jour. Bienvenue !');
    }
}
