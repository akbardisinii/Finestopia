<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'amount',
        'date',
        'description',
        'category_id'
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Category (sebelumnya Income)
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    // Scope untuk mendapatkan expense berdasarkan user
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Accessor untuk format amount
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2, ',', '.');
    }
}