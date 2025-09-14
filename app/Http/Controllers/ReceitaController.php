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
        $receitas = Auth::user()->receitas()
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
        return Inertia::render('Receitas/Create');
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
        ]);

        Auth::user()->receitas()->create($validated);

        return redirect()->route('receitas.index')
            ->with('success', 'Receita criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Receita $receita): Response
    {
        $this->authorize('view', $receita);

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

        return Inertia::render('Receitas/Edit', [
            'receita' => $receita
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
