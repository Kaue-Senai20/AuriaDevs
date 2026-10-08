<?php

use App\Models\Collection;
use App\Models\Loan;
use App\Models\Log;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

// API do Auria Books — consumida pelo bot de WhatsApp (header X-API-Token).
Route::middleware('api.token')->group(function () {

    // Login (confere e-mail + senha bcrypt)
    Route::post('/login', function (Request $r) {
        $u = User::where('email', $r->input('email'))->first();
        if (!$u || !Hash::check((string) $r->input('password'), $u->password)) {
            return response()->json(['error' => 'Credenciais de login incorretas!'], 401);
        }
        return response()->json($u->makeHidden('password'));
    });

    // Usuários
    Route::get('/usuarios', fn () => User::select('id', 'name', 'email', 'telephone', 'class', 'role', 'birthday')->get());
    Route::get('/usuarios/{id}', function ($id) {
        $u = User::select('id', 'name', 'email', 'telephone', 'class', 'role', 'birthday')->find($id);
        return $u ?? response()->json(['error' => 'Usuário não encontrado'], 404);
    });

    // Acervo (?disponivel=1 filtra só disponíveis)
    Route::get('/acervo', function (Request $r) {
        $q = Collection::query();
        if ($r->boolean('disponivel')) {
            $q->where('status', 'available')->whereRaw('borrowed_quantity < quantity');
        }
        return $q->get();
    });

    // Atrasados: emprestado + passou do vencimento (pro bot cobrar)
    Route::get('/atrasados', function () {
        return Loan::with(['user:id,name,telephone', 'book:id,title'])
            ->where('status', 'borrowed')
            ->where('due_date', '<', now())
            ->get()
            ->map(fn ($l) => [
                'emprestimo_id' => $l->id,
                'usuario' => $l->user?->name,
                'telefone' => $l->user?->telephone,
                'livro' => $l->book?->title,
                'dias_atraso' => now()->diffInDays($l->due_date),
            ]);
    });

    // Lembretes: vencem amanhã (pro bot avisar 1 dia antes)
    Route::get('/lembretes', function () {
        return Loan::with(['user:id,name,telephone', 'book:id,title'])
            ->where('status', 'borrowed')
            ->whereDate('due_date', now()->addDay()->toDateString())
            ->get()
            ->map(fn ($l) => [
                'emprestimo_id' => $l->id,
                'usuario' => $l->user?->name,
                'telefone' => $l->user?->telephone,
                'livro' => $l->book?->title,
            ]);
    });

    // Histórico de um aluno (feed dos logs: inclui interesse e renovação)
    Route::get('/historico/{userId}', function ($userId) {
        return Log::where('performed_by', $userId)->orderByDesc('date')->get();
    });

    // Manifestar interesse num livro (grava log 'interested')
    Route::post('/interesse', function (Request $r) {
        $r->validate(['user_id' => 'required|exists:users,id', 'book_id' => 'required|exists:collection,id']);
        $livro = Collection::find($r->input('book_id'));
        $log = Log::create([
            'date' => now(),
            'type' => 'loan',
            'performed_by' => $r->input('user_id'),
            'title' => $livro->title,
            'status' => 'interested',
            'description' => 'Interesse manifestado pelo app',
        ]);
        return response()->json($log, 201);
    });
});
