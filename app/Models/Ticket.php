<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table = 'tickets';
    protected $primaryKey = 'id_ticket';

    protected $fillable = [
        'folio',
        'titulo',
        'solicitante',
        'descripcion',
        'prioridad',
        'estado',
        'tipo_formato',
        'creado_por',
        'asignado_a',
        'asignado_por',
        'id_servicio',
    ];

    // Relaciones
    public function creador()
    {
        return $this->belongsTo(Cuenta::class, 'creado_por', 'id_cuenta');
    }

public function asignadoA()
{
    return $this->belongsTo(\App\Models\Cuenta::class, 'asignado_a', 'id_cuenta');
}

public function creadoPor()
{
    return $this->belongsTo(\App\Models\Cuenta::class, 'creado_por', 'id_cuenta');
}

public function asignadoPor()
{
    return $this->belongsTo(\App\Models\Cuenta::class, 'asignado_por', 'id_cuenta');
}


    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'id_servicio', 'id_servicio');
    }
}
