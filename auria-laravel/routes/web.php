<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
});

Route::view('/login', 'login');
Route::view('/cadastro', 'cadastro');
Route::view('/verificacao-email', 'verificacao-email');
Route::view('/esqueceu-senha', 'esqueceu-senha');
Route::view('/redefinir-senha', 'redefinir-senha');
Route::view('/home', 'home');
Route::view('/perfil', 'perfil');
Route::view('/dashboard', 'dashboard');
Route::view('/gerenciamento-acervo', 'gerenciamento-acervo');
Route::view('/gerenciamento-usuarios', 'gerenciamento-usuarios');
Route::view('/relatorios', 'relatorios');
