<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receita extends Model
{
    protected $fillable = [
        'user_id',
        'descricao',
        'valor',
        'tipo',
        'frequencia',
        'status',
        'data_recebimento',
        'data_vencimento',
        'observacoes',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'data_recebimento' => 'date',
        'data_vencimento' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
