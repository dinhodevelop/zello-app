<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Despesa extends Model
{
    protected $fillable = [
        'user_id',
        'descricao',
        'valor',
        'tipo',
        'categoria',
        'status',
        'data_pagamento',
        'data_vencimento',
        'recorrente',
        'observacoes',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'data_pagamento' => 'date',
        'data_vencimento' => 'date',
        'recorrente' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
