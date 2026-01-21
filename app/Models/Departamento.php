<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Departamento extends Model
{
    protected $table = 'departamentos';
    protected $primaryKey = 'id_departamento';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
        'activo',
    ];


public function servicios()
    {
        return $this->hasMany(Servicio::class, 'id_departamento', 'id_departamento');
    }


public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'id_departamento', 'id_departamento');
    }


    public function tickets()
{
    return $this->hasMany(Ticket::class, 'id_departamento', 'id_departamento');
}

}




        

