<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('despesas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('descricao');
            $table->decimal('valor', 10, 2);
            $table->enum('tipo', ['fixa', 'variavel']);
            $table->string('categoria'); // aluguel, financiamento, assinatura, mercado, combustivel, lazer, etc.
            $table->enum('status', ['pago', 'pendente', 'vencido']);
            $table->date('data_pagamento')->nullable();
            $table->date('data_vencimento');
            $table->boolean('recorrente')->default(false);
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('despesas');
    }
};
