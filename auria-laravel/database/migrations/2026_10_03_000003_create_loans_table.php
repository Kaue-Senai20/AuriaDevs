<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Espelha a tabela `loans` do dump auria_books.sql.
// Interesse/Renovado vivem em `logs`, não aqui (decisão do DB do grupo).
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('collection');
            $table->foreignId('user_id')->constrained('users');
            $table->dateTime('loan_date');
            $table->dateTime('due_date');
            $table->dateTime('return_date')->nullable();
            $table->enum('status', ['borrowed', 'returned', 'overdue'])->default('borrowed');
            $table->index(['book_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
