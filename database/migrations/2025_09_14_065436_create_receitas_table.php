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
        Schema::create('receitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('descricao');
            $table->decimal('valor', 10, 2);
            $table->enum('tipo', ['salario', 'freelance', 'rendimento', 'outros']);
            $table->enum('frequencia', ['mensal', 'semanal', 'unica']);
            $table->enum('status', ['recebido', 'pendente']);
            $table->date('data_recebimento')->nullable();
            $table->date('data_vencimento')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receitas');
    }
};
