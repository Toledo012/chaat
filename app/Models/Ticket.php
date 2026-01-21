<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table = 'tickets';
    protected $primaryKey = 'id_ticket';

    protected $fillable = [
        'id_departamento',
        'nombre_solicitante',
        'telefono',
        'correo_solicitante',
        'asunto',
        'descripcion',
        'tipo_atencion',
        'creado_por_tipo',
        'id_usuario_creador',
        'id_tecnico_asignado',
        'tomado_en',
        'asignado_por',
        'asignado_en',
        'estado',
        'formato_requerido',
        'id_servicio',
        'formato_generado_en',
        'cerrado_en',
    ];

    // Relaciones
    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'id_departamento');
    }

    public function creador()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_creador');
    }

    public function tecnico()
    {
        return $this->belongsTo(Usuario::class, 'id_tecnico_asignado');
    }

    public function asignador()
    {
        return $this->belongsTo(Usuario::class, 'asignado_por');
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'id_servicio');
    }
}
