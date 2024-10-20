<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Allocation extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'primary', 'secondary', 'investment', 'debt', 'total'];

    // Ensure these values are always cast as floats
    protected $casts = [
        'primary' => 'float',
        'secondary' => 'float',
        'investment' => 'float',
        'debt' => 'float',
        'total' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper method to calculate percentages
    public function getPercentages()
    {
        if ($this->total == 0) {
            return [
                'primary' => 0,
                'secondary' => 0,
                'investment' => 0,
                'debt' => 0,
            ];
        }

        return [
            'primary' => ($this->primary / $this->total) * 100,
            'secondary' => ($this->secondary / $this->total) * 100,
            'investment' => ($this->investment / $this->total) * 100,
            'debt' => ($this->debt / $this->total) * 100,
        ];
    }
}