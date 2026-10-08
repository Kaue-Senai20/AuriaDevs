<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Espelha a tabela `logs` do dump auria_books.sql.
// É o feed unificado do Histórico (perfil) e dos Relatórios:
// inclui 'renewed' e 'interested', que não existem em `loans`.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date');
            $table->enum('type', ['loan', 'collection', 'user']);
            $table->foreignId('performed_by')->constrained('users');
            $table->string('title', 100);
            $table->enum('status', ['borrowed', 'returned', 'overdue', 'renewed', 'interested', 'created', 'removed', 'updated']);
            $table->string('description', 500)->nullable();
            $table->index('performed_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
