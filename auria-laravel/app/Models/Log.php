<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $table = 'logs';

    public $timestamps = false;

    protected $fillable = [
        'date', 'type', 'performed_by', 'title', 'status', 'description',
    ];

    protected function casts(): array
    {
        return ['date' => 'datetime'];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
