<?php
// app/Models/Usuario.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use HasFactory;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    
    protected $fillable = [
        'nombre',
        'puesto',
        'id_departamento',
        'extension',
        'email'
    ];

    public $timestamps = true;

    // Relación con Cuentas
    public function cuenta()
    {
        return $this->hasOne(Cuenta::class, 'id_usuario');
    }

    // Relación con Servicios
    public function servicios()
    {
        return $this->hasMany(Servicio::class, 'id_usuario');
    }


    // Relación con Departamento    
    public function departamentos()
{
    return $this->belongsTo(Departamento::class, 'id_departamento');
}

}


