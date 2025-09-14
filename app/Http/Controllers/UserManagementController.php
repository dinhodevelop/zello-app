<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Household;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;

class UserManagementController extends Controller
{
    public function index()
    {
        $currentHousehold = Auth::user()->household;
        
        if (!$currentHousehold) {
            return redirect()->route('household.index')
                ->with('error', 'Você precisa estar em um lar para gerenciar membros.');
        }

        $members = $currentHousehold->users()->get();
        $allUsers = User::whereNull('household_id')
            ->orWhere('household_id', '!=', $currentHousehold->id)
            ->get();

        return Inertia::render('UserManagement/Index', [
            'household' => $currentHousehold,
            'members' => $members,
            'availableUsers' => $allUsers
        ]);
    }

    public function create()
    {
        return Inertia::render('UserManagement/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'add_to_household' => 'boolean'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'household_id' => $request->add_to_household ? Auth::user()->household_id : null
        ]);

        $message = 'Usuário criado com sucesso!';
        if ($request->add_to_household) {
            $message .= ' O usuário foi adicionado ao seu lar.';
        }

        return redirect()->route('user-management.index')
            ->with('success', $message);
    }

    public function addToHousehold(Request $request, User $user)
    {
        $currentHousehold = Auth::user()->household;
        
        if (!$currentHousehold) {
            return redirect()->back()
                ->with('error', 'Você precisa estar em um lar para adicionar membros.');
        }

        if ($user->household_id === $currentHousehold->id) {
            return redirect()->back()
                ->with('info', 'Este usuário já pertence ao seu lar.');
        }

        $user->update(['household_id' => $currentHousehold->id]);

        return redirect()->back()
            ->with('success', $user->name . ' foi adicionado ao lar "' . $currentHousehold->name . '"');
    }

    public function removeFromHousehold(Request $request, User $user)
    {
        $currentHousehold = Auth::user()->household;
        
        if (!$currentHousehold || $user->household_id !== $currentHousehold->id) {
            return redirect()->back()
                ->with('error', 'Este usuário não pertence ao seu lar.');
        }

        // Não permite remover o criador do lar
        if ($user->id === $currentHousehold->created_by) {
            return redirect()->back()
                ->with('error', 'Não é possível remover o criador do lar.');
        }

        $user->update(['household_id' => null]);

        return redirect()->back()
            ->with('success', $user->name . ' foi removido do lar.');
    }

    public function moveToHousehold(Request $request, User $user)
    {
        $request->validate([
            'household_id' => 'required|exists:households,id'
        ]);

        $targetHousehold = Household::find($request->household_id);
        
        if ($user->household_id === $targetHousehold->id) {
            return redirect()->back()
                ->with('info', 'Este usuário já pertence ao lar selecionado.');
        }

        $user->update(['household_id' => $targetHousehold->id]);

        return redirect()->back()
            ->with('success', $user->name . ' foi movido para o lar "' . $targetHousehold->name . '"');
    }

    public function updateRole(Request $request, User $user)
    {
        // Verifica permissões usando Policy
        $this->authorize('updateRole', $user);

        $request->validate([
            'role' => 'required|in:admin,user'
        ]);

        $oldRole = $user->role;
        $newRole = $request->role;

        $user->update(['role' => $newRole]);

        $roleNames = [
            'admin' => 'Administrador',
            'user' => 'Usuário'
        ];

        return redirect()->back()
            ->with('success', 'Role de ' . $user->name . ' alterada para ' . $roleNames[$newRole] . ' com sucesso.');
    }

    public function destroy(User $user)
    {
        // Verifica permissões usando Policy
        $this->authorize('delete', $user);

        // Não permite excluir criadores de lares
        $isCreator = Household::where('created_by', $user->id)->exists();
        if ($isCreator) {
            return redirect()->back()
                ->with('error', 'Não é possível excluir usuários que são criadores de lares.');
        }

        $user->delete();

        return redirect()->back()
            ->with('success', 'Usuário excluído com sucesso.');
    }
}