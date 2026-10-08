<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Espelha a tabela `users` do dump auria_books.sql (amigo/DB).
// Senha sempre em bcrypt: cast 'hashed' no Model (BCRYPT_ROUNDS=12 gera $2y$12$).
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 75);
            $table->string('email', 50)->unique();
            $table->string('telephone', 20);
            $table->string('class', 15);
            $table->enum('role', ['user', 'admin'])->default('user');
            $table->string('password', 128);
            $table->string('image_path', 100)->nullable();
            $table->date('birthday');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
