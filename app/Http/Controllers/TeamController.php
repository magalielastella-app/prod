<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\Positions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class TeamController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorizeAdmin($request);

        $users = User::query()
            ->orderBy('role')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role', 'position', 'department', 'manager_id', 'hired_on'])
            ->map(fn (User $u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'role' => $u->role,
                'position' => $u->position,
                'department' => $u->department,
                'manager_id' => $u->manager_id,
                'hired_on' => optional($u->hired_on)->toDateString(),
            ]);

        $managers = User::query()
            ->whereIn('role', [User::ROLE_MANAGER, User::ROLE_ADMIN])
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Team/Index', [
            'users' => $users,
            'managers' => $managers,
            'positions' => Positions::list(),
            'roles' => [
                ['value' => User::ROLE_EMPLOYEE, 'label' => 'Salarié'],
                ['value' => User::ROLE_MANAGER, 'label' => 'Manager'],
                ['value' => User::ROLE_ADMIN, 'label' => 'Administrateur'],
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $data = $this->validateUser($request);
        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return redirect()->route('team.index')->with('success', 'Membre ajouté');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $data = $this->validateUser($request, $user);
        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('team.index')->with('success', 'Membre mis à jour');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->authorizeAdmin($request);

        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Vous ne pouvez pas vous supprimer vous-même.');
        }

        $user->delete();

        return back()->with('success', 'Membre supprimé');
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless(optional($request->user())->isAdmin(), 403);
    }

    private function validateUser(Request $request, ?User $user = null): array
    {
        $emailRule = ['required', 'email', 'max:255'];
        $emailRule[] = Rule::unique('users', 'email')->ignore($user?->id);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => $emailRule,
            'role' => ['required', Rule::in([User::ROLE_EMPLOYEE, User::ROLE_MANAGER, User::ROLE_ADMIN])],
            'position' => ['required', Rule::in(Positions::list())],
            'department' => ['nullable', 'string', 'max:255'],
            'manager_id' => ['nullable', 'exists:users,id'],
            'hired_on' => ['nullable', 'date'],
            'password' => [$user ? 'nullable' : 'required', 'confirmed', Rules\Password::defaults()],
        ];

        return $request->validate($rules);
    }
}
