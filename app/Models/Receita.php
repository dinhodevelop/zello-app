<?php

namespace App\Models;

use App\Traits\BelongsToHousehold;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receita extends Model
{
    use BelongsToHousehold;
    protected $fillable = [
        'user_id',
        'household_id',
        'created_by',
        'responsible_user_id',
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function responsibleUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_user_id');
    }
}
