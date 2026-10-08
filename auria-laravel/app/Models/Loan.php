<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $table = 'loans';

    public $timestamps = false;

    protected $fillable = [
        'book_id', 'user_id', 'loan_date', 'due_date', 'return_date', 'status',
    ];

    protected function casts(): array
    {
        return [
            'loan_date' => 'datetime',
            'due_date' => 'datetime',
            'return_date' => 'datetime',
        ];
    }

    public function book()
    {
        return $this->belongsTo(Collection::class, 'book_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
