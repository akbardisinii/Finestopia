<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'amount', 'description', 'due_date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}