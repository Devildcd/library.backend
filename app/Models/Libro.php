<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class Libro extends Model
{
    use HasFactory;

    public function imagen() {
        return $this->hasOne(ImagenFile::class);
    }

    public function documento() {
        return $this->hasOne(DocFile::class);
    }

    protected $fillable = [
        'nombre',
        'autor',
        'codigo',
        'descripcion',
        'tipo',
        'descargable',
        'principal',
        'ubicacion',
        'url'
    ];

    protected $casts = [
        'descargable' => 'boolean',
        'principal' => 'boolean'
    ];

    public static $tipoValues = [
        'digital',
        'físico',
        'externo'
    ];

    public static function rules() {
        return [
            'nombre' => ['required'],
            'autor' => ['required'],
            'codigo' => ['required'],
            'descripcion' => ['required'],
            'tipo' => [
                Rule::in(self::$tipoValues)
            ],
       
            'ubicacion' => [
                function ($attribute, $value, $fail) {
                    if (request()->tipo === 'físico' && empty($value)) {
                        $fail('El campo ubicacion es requerido cuando el tipo es físico.');
                    }
                },
                'max:100'
            ],

            'url' => [
                function ($attribute, $value, $fail) {
                    if (request()->tipo === 'externo' && empty($value)) {
                        $fail('El campo URL es requerido cuando el tipo es externo.');
                    }
                },
            ],
        ];
        
    }
}
