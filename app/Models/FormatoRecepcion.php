<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormatoRecepcion extends Model
{
    protected $table = 'formato_recepcion';
    protected $primaryKey = 'id_formato_r';

    protected $fillable = [
        'id_servicio',
        'descripcion',
        'firma_usuario',
        'firma_tecnico',
    ];

    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'id_servicio', 'id_servicio');
    }
}
