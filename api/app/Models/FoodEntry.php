<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'food_name',
        'calories',
        'protein',
        'carbohydrates',
        'fat',
        'fiber',
        'sugar',
        'serving_size',
        'serving_unit',
        'entry_date',
    ];

    protected $casts = [
        'calories' => 'integer',
        'protein' => 'float',
        'carbohydrates' => 'float',
        'fat' => 'float',
        'fiber' => 'float',
        'sugar' => 'float',
        'serving_size' => 'float',
        'entry_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}