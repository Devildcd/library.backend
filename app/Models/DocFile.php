<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocFile extends Model
{
    use HasFactory;

    public function libro() {
        return $this->belongsTo(Libro::class);
    }

    protected $fillable = [
        'libro_id',
        'doc',
        
    ];

    public static function rules() {
        return [
            'doc' => ['file', 'mimes:pdf,doc,docx,txt', 'max:2048', 'required'],
            
            
        ];
    }
}
