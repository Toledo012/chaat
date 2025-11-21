<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogoMateriales extends Model
{
    protected $table = 'catalogo_materiales';       // nombre real de la tabla
    protected $primaryKey = 'id_material';          // clave primaria correcta
    public $incrementing = true;                    // sí es autoincremental
    protected $keyType = 'int';                     // tipo entero
    public $timestamps = false; // ←    no hay timestamps

    protected $fillable = [
        'nombre',
        'unidad_sugerida',
    ];
}
    