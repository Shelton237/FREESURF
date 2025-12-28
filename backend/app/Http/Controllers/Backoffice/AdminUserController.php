<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminUserStoreRequest;
use App\Http\Requests\AdminUserUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AdminUserController extends Controller
{
    public function index(): Response
    {
        $users = User::orderByDesc('id')->paginate(10)->withQueryString();
        return Inertia::render('Backoffice/Admin/Users/Index', [
            'users' => $users,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Backoffice/Admin/Users/Create');
    }

    public function store(AdminUserStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        User::create($data);
        return redirect()->route('backoffice.admin.users.index')->with('success', 'Utilisateur créé');
    }

    public function edit(User $user): Response
    {
        return Inertia::render('Backoffice/Admin/Users/Edit', [
            'userData' => $user,
        ]);
    }

    public function update(AdminUserUpdateRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        if (empty($data['password'])) {
            unset($data['password']);
        }
        $user->update($data);
        return redirect()->route('backoffice.admin.users.index')->with('success', 'Utilisateur mis à jour');
    }

    public function destroy(User $user): RedirectResponse
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Impossible de supprimer votre propre compte');
        }
        $user->delete();
        return back()->with('success', 'Utilisateur supprimé');
    }
}

