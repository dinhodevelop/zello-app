<?php

namespace App\Http\Controllers;

use App\Models\Receita;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;


class ReceitaController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $receitas = Receita::forCurrentHousehold()
            ->with(['creator', 'responsibleUser'])
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Receitas/Index', [
            'receitas' => $receitas
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $householdUsers = Auth::user()->household->users ?? collect();
        
        return Inertia::render('Receitas/Create', [
            'householdUsers' => $householdUsers
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'descricao' => 'required|string|max:255',
            'valor' => 'required|numeric|min:0',
            'tipo' => 'required|in:salario,freelance,rendimento,outros',
            'frequencia' => 'required|in:mensal,semanal,unica',
            'status' => 'required|in:recebido,pendente',
            'data_recebimento' => 'nullable|date',
            'data_vencimento' => 'nullable|date',
            'observacoes' => 'nullable|string',
            'responsible_user_id' => 'nullable|exists:users,id',
        ]);

        $receitaData = array_merge($validated, [
            'user_id' => Auth::id(),
            'created_by' => Auth::id(),
            'responsible_user_id' => $validated['responsible_user_id'] ?? Auth::id(),
        ]);

        Receita::create($receitaData);

        return redirect()->route('receitas.index')
            ->with('success', 'Receita criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Receita $receita): Response
    {
        $this->authorize('view', $receita);

        $receita->load(['creator', 'responsibleUser']);

        return Inertia::render('Receitas/Show', [
            'receita' => $receita
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Receita $receita): Response
    {
        $this->authorize('update', $receita);

        $householdUsers = Auth::user()->household->users ?? collect();
        $receita->load(['creator', 'responsibleUser']);

        return Inertia::render('Receitas/Edit', [
            'receita' => $receita,
            'householdUsers' => $householdUsers
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Receita $receita)
    {
        $this->authorize('update', $receita);

        $validated = $request->validate([
            'descricao' => 'required|string|max:255',
            'valor' => 'required|numeric|min:0',
            'tipo' => 'required|in:salario,freelance,rendimento,outros',
            'frequencia' => 'required|in:mensal,semanal,unica',
            'status' => 'required|in:recebido,pendente',
            'data_recebimento' => 'nullable|date',
            'data_vencimento' => 'nullable|date',
            'observacoes' => 'nullable|string',
            'responsible_user_id' => 'nullable|exists:users,id',
        ]);

        $receita->update($validated);

        return redirect()->route('receitas.index')
            ->with('success', 'Receita atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Receita $receita)
    {
        $this->authorize('delete', $receita);

        $receita->delete();

        return redirect()->route('receitas.index')
            ->with('success', 'Receita excluída com sucesso!');
    }
}
