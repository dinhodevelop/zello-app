<?php

namespace App\Http\Controllers;

use App\Models\Receita;
use App\Models\Despesa;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $user = Auth::user();
        
        // Obter filtros da request
        $periodo = $request->get('periodo', 'mes_atual'); // mes_atual, mes_anterior, ano_atual, personalizado
        $dataInicio = $request->get('data_inicio');
        $dataFim = $request->get('data_fim');
        
        // Definir datas baseado no período
        [$startDate, $endDate] = $this->getDateRange($periodo, $dataInicio, $dataFim);
        
        // Buscar receitas do período
        $receitasQuery = Receita::forCurrentHousehold();
        if ($startDate && $endDate) {
            $receitasQuery->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        $receitas = $receitasQuery->get();
        $receitasRecentes = Receita::forCurrentHousehold()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Buscar despesas do período
        $despesasQuery = Despesa::forCurrentHousehold();
        if ($startDate && $endDate) {
            $despesasQuery->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        $despesas = $despesasQuery->get();
        $despesasPendentes = Despesa::forCurrentHousehold()
            ->where('status', 'pendente')
            ->orderBy('data_vencimento', 'asc')
            ->limit(5)
            ->get();
        
        // Calcular totais
        $totalReceitas = $receitas->sum('valor');
        $totalDespesas = $despesas->sum('valor');
        $saldo = $totalReceitas - $totalDespesas;
        
        // Estatísticas por status
        $receitasRecebidas = $receitas->where('status', 'recebido')->sum('valor');
        $receitasPendentes = $receitas->where('status', 'pendente')->sum('valor');
        
        $despesasPagas = $despesas->where('status', 'pago')->sum('valor');
        $despesasPendentesValor = $despesas->where('status', 'pendente')->sum('valor');
        $despesasVencidas = $despesas->where('status', 'vencido')->sum('valor');
        
        // Estatísticas por categoria (despesas)
        $despesasPorCategoria = $despesas->groupBy('categoria')
            ->map(function ($items) {
                return [
                    'categoria' => $items->first()->categoria,
                    'total' => $items->sum('valor'),
                    'quantidade' => $items->count()
                ];
            })->values();
        
        // Estatísticas por tipo (receitas)
        $receitasPorTipo = $receitas->groupBy('tipo')
            ->map(function ($items) {
                return [
                    'tipo' => $items->first()->tipo,
                    'total' => $items->sum('valor'),
                    'quantidade' => $items->count()
                ];
            })->values();
        
        // Dados para gráfico mensal (últimos 6 meses)
        $dadosGrafico = $this->getDadosGraficoMensal();
        
        return Inertia::render('Dashboard', [
            'filtros' => [
                'periodo' => $periodo,
                'data_inicio' => $dataInicio,
                'data_fim' => $dataFim,
                'periodo_label' => $this->getPeriodoLabel($periodo, $startDate, $endDate)
            ],
            'resumo' => [
                'total_receitas' => $totalReceitas,
                'total_despesas' => $totalDespesas,
                'saldo' => $saldo,
                'receitas_recebidas' => $receitasRecebidas,
                'receitas_pendentes' => $receitasPendentes,
                'despesas_pagas' => $despesasPagas,
                'despesas_pendentes' => $despesasPendentesValor,
                'despesas_vencidas' => $despesasVencidas,
            ],
            'estatisticas' => [
                'despesas_por_categoria' => $despesasPorCategoria,
                'receitas_por_tipo' => $receitasPorTipo,
                'dados_grafico' => $dadosGrafico,
            ],
            'listas' => [
                'receitas_recentes' => $receitasRecentes,
                'despesas_pendentes' => $despesasPendentes,
            ]
        ]);
    }
    
    private function getDateRange($periodo, $dataInicio = null, $dataFim = null): array
    {
        $now = Carbon::now();
        
        switch ($periodo) {
            case 'mes_atual':
                return [$now->startOfMonth()->toDateString(), $now->endOfMonth()->toDateString()];
                
            case 'mes_anterior':
                $mesAnterior = $now->subMonth();
                return [$mesAnterior->startOfMonth()->toDateString(), $mesAnterior->endOfMonth()->toDateString()];
                
            case 'ano_atual':
                return [$now->startOfYear()->toDateString(), $now->endOfYear()->toDateString()];
                
            case 'ultimos_30_dias':
                return [$now->subDays(30)->toDateString(), Carbon::now()->toDateString()];
                
            case 'ultimos_90_dias':
                return [$now->subDays(90)->toDateString(), Carbon::now()->toDateString()];
                
            case 'personalizado':
                if ($dataInicio && $dataFim) {
                    return [$dataInicio, $dataFim];
                }
                return [null, null];
                
            default:
                return [null, null];
        }
    }
    
    private function getPeriodoLabel($periodo, $startDate = null, $endDate = null): string
    {
        switch ($periodo) {
            case 'mes_atual':
                return 'Mês Atual (' . Carbon::now()->format('M/Y') . ')';
            case 'mes_anterior':
                return 'Mês Anterior (' . Carbon::now()->subMonth()->format('M/Y') . ')';
            case 'ano_atual':
                return 'Ano Atual (' . Carbon::now()->format('Y') . ')';
            case 'ultimos_30_dias':
                return 'Últimos 30 dias';
            case 'ultimos_90_dias':
                return 'Últimos 90 dias';
            case 'personalizado':
                if ($startDate && $endDate) {
                    return Carbon::parse($startDate)->format('d/m/Y') . ' - ' . Carbon::parse($endDate)->format('d/m/Y');
                }
                return 'Período Personalizado';
            default:
                return 'Todos os períodos';
        }
    }
    
    private function getDadosGraficoMensal(): array
    {
        $dados = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $mes = Carbon::now()->subMonths($i);
            $inicioMes = $mes->copy()->startOfMonth();
            $fimMes = $mes->copy()->endOfMonth();
            
            $receitasMes = Receita::forCurrentHousehold()
                ->whereBetween('created_at', [$inicioMes, $fimMes])
                ->sum('valor');
                
            $despesasMes = Despesa::forCurrentHousehold()
                ->whereBetween('created_at', [$inicioMes, $fimMes])
                ->sum('valor');
            
            $dados[] = [
                'mes' => $mes->format('M/Y'),
                'receitas' => (float) $receitasMes,
                'despesas' => (float) $despesasMes,
                'saldo' => (float) ($receitasMes - $despesasMes)
            ];
        }
        
        return $dados;
    }
}