<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RespuestaUsuario extends Model
{
    use HasFactory;

    protected $table = 'respuesta_usuarios';
    protected $fillable = ['id_pregunta', 'respuesta_usuario', 'correcta']; // Añade 'correcta' aquí
}