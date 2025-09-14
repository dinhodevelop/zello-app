<?php

namespace App\Http\Controllers;

use App\Models\Household;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HouseholdController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if (!$user->household_id) {
            return Inertia::render('Household/Create');
        }

        return Inertia::render('Household/Show', [
            'household' => $user->household->load('users')
        ]);
    }

    public function create()
    {
        return Inertia::render('Household/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $household = Household::create([
            'name' => $request->name,
            'created_by' => Auth::id(),
        ]);

        // Atualiza o usuário para pertencer ao lar criado
        DB::table('users')
            ->where('id', Auth::id())
            ->update(['household_id' => $household->id]);

        return redirect()->route('household.index')
            ->with('success', 'Lar familiar criado com sucesso!');
    }

    public function show(Household $household)
    {
        // Verifica se o usuário pertence a este lar
        if (Auth::user()->household_id !== $household->id) {
            abort(403);
        }

        return Inertia::render('Household/Show', [
            'household' => $household->load('users')
        ]);
    }

    public function edit(Household $household)
    {
        // Verifica se o usuário pertence a este lar
        if (Auth::user()->household_id !== $household->id) {
            abort(403);
        }

        return Inertia::render('Household/Edit', [
            'household' => $household
        ]);
    }

    public function update(Request $request, Household $household)
    {
        // Verifica se o usuário pertence a este lar
        if (Auth::user()->household_id !== $household->id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $household->update([
            'name' => $request->name,
        ]);

        return redirect()->route('household.index')
            ->with('success', 'Lar familiar atualizado com sucesso!');
    }

    public function leave()
    {
        DB::table('users')
            ->where('id', Auth::id())
            ->update(['household_id' => null]);

        return redirect()->route('household.index')
            ->with('success', 'Você saiu do lar familiar.');
    }

    public function available()
    {
        // Lista todos os lares disponíveis (simplificado - todos os lares públicos)
        $households = Household::with('users')->get();

        return Inertia::render('Household/Available', [
            'households' => $households
        ]);
    }

    public function join(Household $household)
    {
        // Verifica se o usuário já não pertence a este lar
        if (Auth::user()->household_id === $household->id) {
            return redirect()->route('household.index')
                ->with('info', 'Você já pertence a este lar.');
        }

        // Atualiza o usuário para pertencer ao lar selecionado
        DB::table('users')
            ->where('id', Auth::id())
            ->update(['household_id' => $household->id]);

        return redirect()->route('household.index')
            ->with('success', 'Você entrou no lar "' . $household->name . '" com sucesso!');
    }

    public function switch(Household $household)
    {
        // Permite trocar rapidamente entre lares
        DB::table('users')
            ->where('id', Auth::id())
            ->update(['household_id' => $household->id]);

        return redirect()->back()
            ->with('success', 'Lar alterado para "' . $household->name . '"');
    }
}
