<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImagenFile extends Model
{
    use HasFactory;

    public function libro() {
        return $this->belongsTo(Libro::class);
    }

    protected $fillable = [
        'libro_id',
        'imagen',
        
    ];

    public static function rules() {
        return [
            'imagen' => ['image', 'max:4048', 'required'],
            
            
        ];
    }
}
