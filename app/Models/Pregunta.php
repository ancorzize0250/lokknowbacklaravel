<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pregunta extends Model
{
    use HasFactory;

    protected $table = 'preguntas';
    protected $fillable = ['pregunta', 'respondida'];

    public function opciones()
    {
        return $this->hasMany(Opcion::class, 'id_pregunta');
    }

    public function respuestaCorrecta()
    {
        return $this->hasOne(RespuestaCorrecta::class, 'id_pregunta');

    }
}