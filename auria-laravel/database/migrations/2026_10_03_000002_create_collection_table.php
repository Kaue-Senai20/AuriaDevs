<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Espelha a tabela `collection` (acervo) do dump auria_books.sql.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collection', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100);
            $table->string('author', 75);
            $table->string('publisher', 30);
            $table->string('publication_year', 4);
            $table->string('genre', 25)->default('Gêneros não definidos.');
            $table->enum('status', ['available', 'unavailable'])->default('available');
            $table->integer('quantity')->default(0);
            $table->integer('borrowed_quantity')->default(0);
            $table->string('synopsis', 1650)->default('A sinopse deste livro não está disponível para visualização no momento.');
            $table->decimal('rating', 2, 1)->default(0.0);
            $table->string('cover_path', 100)->default('/images/covers/default.jpg');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collection');
    }
};
