<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    use HasFactory;

    protected $table = 'roles'; // nombre exacto de la tabla

    protected $primaryKey = 'id_rol'; // tu clave primaria

    protected $fillable = [
        'nombre', // o el nombre del campo real
    ];

    public $timestamps = false; // si tu tabla no tiene created_at / updated_at
}
