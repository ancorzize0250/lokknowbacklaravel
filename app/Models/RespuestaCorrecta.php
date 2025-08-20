<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RespuestaCorrecta extends Model
{
    use HasFactory;

    protected $table = 'respuesta_correctas';
    protected $fillable = ['id_pregunta', 'opcion_correcta'];
}