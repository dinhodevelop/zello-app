<?php

namespace App\Http\Controllers;

use App\Models\Despesa;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class DespesaController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $despesas = Despesa::forCurrentHousehold()
            ->with(['creator', 'responsibleUser'])
            ->orderBy('data_vencimento', 'asc')
            ->get();

        return Inertia::render('Despesas/Index', [
            'despesas' => $despesas
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $householdUsers = Auth::user()->household->users ?? collect();
        
        return Inertia::render('Despesas/Create', [
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
            'tipo' => 'required|in:fixa,variavel',
            'categoria' => 'required|string|max:255',
            'status' => 'required|in:pago,pendente,vencido',
            'data_pagamento' => 'nullable|date',
            'data_vencimento' => 'required|date',
            'recorrente' => 'boolean',
            'observacoes' => 'nullable|string',
            'responsible_user_id' => 'nullable|exists:users,id',
        ]);

        $despesaData = array_merge($validated, [
            'user_id' => Auth::id(),
            'created_by' => Auth::id(),
            'responsible_user_id' => $validated['responsible_user_id'] ?? Auth::id(),
        ]);

        Despesa::create($despesaData);

        return redirect()->route('despesas.index')
            ->with('success', 'Despesa criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Despesa $despesa): Response
    {
        $this->authorize('view', $despesa);

        $despesa->load(['creator', 'responsibleUser']);

        return Inertia::render('Despesas/Show', [
            'despesa' => $despesa
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Despesa $despesa): Response
    {
        $this->authorize('update', $despesa);

        $householdUsers = Auth::user()->household->users ?? collect();
        $despesa->load(['creator', 'responsibleUser']);

        return Inertia::render('Despesas/Edit', [
            'despesa' => $despesa,
            'householdUsers' => $householdUsers
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Despesa $despesa)
    {
        $this->authorize('update', $despesa);

        $validated = $request->validate([
            'descricao' => 'required|string|max:255',
            'valor' => 'required|numeric|min:0',
            'tipo' => 'required|in:fixa,variavel',
            'categoria' => 'required|string|max:255',
            'status' => 'required|in:pago,pendente,vencido',
            'data_pagamento' => 'nullable|date',
            'data_vencimento' => 'required|date',
            'recorrente' => 'boolean',
            'observacoes' => 'nullable|string',
            'responsible_user_id' => 'nullable|exists:users,id',
        ]);

        $despesa->update($validated);

        return redirect()->route('despesas.index')
            ->with('success', 'Despesa atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Despesa $despesa)
    {
        $this->authorize('delete', $despesa);

        $despesa->delete();

        return redirect()->route('despesas.index')
            ->with('success', 'Despesa excluída com sucesso!');
    }
}
