<?php
// app/Models/Servicio.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    use HasFactory;

    protected $table = 'servicios';
    protected $primaryKey = 'id_servicio';
    
    protected $fillable = [
        'folio',
        'fecha',
        'id_usuario',
        'tipo_formato'
    ];

    public $timestamps = true;

    // Relación con Usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    // Relaciones con los formatos
    public function formatoA()
    {
        return $this->hasOne(FormatoA::class, 'id_servicio');
    }

    public function formatoB()
    {
        return $this->hasOne(FormatoB::class, 'id_servicio');
    }

    public function formatoC()
    {
        return $this->hasOne(FormatoC::class, 'id_servicio');
    }

    public function formatoD()
    {
        return $this->hasOne(FormatoD::class, 'id_servicio');
    }
}
