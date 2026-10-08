<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    protected $table = 'collection';

    public $timestamps = false;

    protected $fillable = [
        'title', 'author', 'publisher', 'publication_year', 'genre',
        'status', 'quantity', 'borrowed_quantity', 'synopsis',
        'rating', 'cover_path',
    ];

    protected function casts(): array
    {
        return ['rating' => 'float'];
    }

    public function loans()
    {
        return $this->hasMany(Loan::class, 'book_id');
    }

    public function disponiveis()
    {
        return $this->quantity - $this->borrowed_quantity;
    }
}
