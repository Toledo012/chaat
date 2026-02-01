<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use \Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Departamento;
use App\Services\ServicioService;
use App\Services\MaterialesService;




class FormatoController extends Controller
{
    // =============================
    // INDEX - LISTA DE FORMATOS
    // =============================
    public function index(Request $request)
    {
      $tipo = $request->input('tipo');
    $usuario = $request->input('usuario');
    $fecha = $request->input('fecha');

    $query = DB::table('servicios')
        ->leftJoin('usuarios', 'usuarios.id_usuario', '=', 'servicios.id_usuario')
        ->select(
            'servicios.id_servicio',
            'servicios.tipo_formato as tipo',
            'servicios.fecha',
            'usuarios.nombre'
        )
        ->orderByDesc('servicios.id_servicio');

    // 🔍 Filtros desde el formulario
    if (!empty($tipo)) {
        $query->where('servicios.tipo_formato', $tipo);
    }
    if (!empty($usuario)) {
        $query->where('usuarios.nombre', 'like', "%$usuario%");
    }
    if (!empty($fecha)) {
        $query->whereDate('servicios.fecha', $fecha);
    }

    // ⚙️ Filtro por usuario autenticado
    $cuenta = Auth::user();

    if (!$cuenta->isAdmin()) {
        // Si no es admin, solo ve los servicios que él mismo creó
        $query->where('servicios.id_usuario', $cuenta->id_usuario);
    }

    $formatos = $query->get();

    return view('admin.formatos.index', compact('formatos', 'tipo', 'usuario', 'fecha'));
    }

    // =============================
    // CREAR NUEVO FORMATO (vista)
    // =============================
    public function create()
    {
        return view('admin.formatos.create');
    }

public function __construct(
    private ServicioService $servicios,
    private MaterialesService $materiales
) {}

public function formatoA(Request $request)
{
    $departamentos = Departamento::where('activo', 1)->get();
    $id_servicio = $request->query('id_servicio');
    $id_ticket = $request->query('id_ticket');

    return view('admin.formatos.formato_a', compact(
        'departamentos',
        'id_servicio',
        'id_ticket'
    ));
}

    public function formatoB()
    {

            $departamentos = Departamento::where('activo', 1)->get();

        return view('admin.formatos.formato_b' , compact('departamentos'));

    }

    public function formatoC()
    
    {
            $departamentos = Departamento::where('activo', 1)->get();

        return view('admin.formatos.formato_c'  , compact('departamentos'));
    }

    public function formatoD()
    {

            $departamentos = Departamento::where('activo', 1)->get();

        return view('admin.formatos.formato_d'  , compact('departamentos'));
    }


    // 

// private function generarFolioGlobal(string $tipoFormato): string
// {
//     $lastFolio = DB::table('servicios')
//         ->orderByDesc('id_servicio')
//         ->lockForUpdate()
//         ->value('folio');

//     $lastNum = 0;
//     if ($lastFolio && preg_match('/SEMAHN-[A-D]-(\d+)/', $lastFolio, $m)) {
//         $lastNum = (int) $m[1];
//     }

//     $nextNum = $lastNum + 1;

//     return 'SEMAHN-' . $tipoFormato . '-' . str_pad((string)$nextNum, 5, '0', STR_PAD_LEFT);
// }





    //logica para guardar formatos A, B, C, D
public function storeA(Request $request)
{
    $data = $request->validate([
        'id_departamento' => 'required|exists:departamentos,id_departamento',

        'subtipo' => 'required|string',

        'tipo_atencion' => 'nullable|string',
        'peticion' => 'nullable|string',

        'tipo_servicio' => 'nullable|string',
        'tipo_servicio_otro' => 'nullable|string|max:255',

        'trabajo_realizado' => 'nullable|string',
        'conclusion_servicio' => 'nullable|string',
        'detalle_realizado' => 'nullable|string',

        'firma_usuario' => 'nullable|string',
        'firma_tecnico' => 'nullable|string',
        'firma_jefe_area' => 'nullable|string',

        'observaciones' => 'nullable|string',

        // vienen desde tickets (hidden)
        'id_servicio' => 'nullable|integer',
        'id_ticket'   => 'nullable|integer',
    ]);

    // Normalizar "otro"
    $tipoServicioFinal = ($data['tipo_servicio'] ?? null) === 'otro'
        ? ($data['tipo_servicio_otro'] ?? null)
        : ($data['tipo_servicio'] ?? null);

    //  sacar antes del transaction
    $idServicioFromRequest = $data['id_servicio'] ?? null;
    $idTicketFromRequest   = $data['id_ticket'] ?? null;

    return DB::transaction(function () use ($data, $tipoServicioFinal, $idServicioFromRequest, $idTicketFromRequest) {

        //  Obtener o crear servicio (si viene de ticket ya debería venir)
        $idServicio = $this->servicios->obtenerOCrearServicio(
            $idServicioFromRequest,
            'A',
            (int) $data['id_departamento']
        );

        //  Insertar Formato A
        DB::table('formato_a')->insert([
            'id_servicio' => $idServicio,
            'subtipo' => $data['subtipo'],
            'tipo_atencion' => $data['tipo_atencion'] ?? null,
            'peticion' => $data['peticion'] ?? null,
            'tipo_servicio' => $tipoServicioFinal,
            'trabajo_realizado' => $data['trabajo_realizado'] ?? null,
            'conclusion_servicio' => $data['conclusion_servicio'] ?? null,
            'detalle_realizado' => $data['detalle_realizado'] ?? null,
            'firma_usuario' => $data['firma_usuario'] ?? null,
            'firma_tecnico' => $data['firma_tecnico'] ?? null,
            'firma_jefe_area' => $data['firma_jefe_area'] ?? null,
            'observaciones' => $data['observaciones'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 
        if ($idTicketFromRequest) {
            $this->servicios->completarTicketPorId((int)$idTicketFromRequest, (int)$idServicio);

            return redirect()->route('admin.tickets.index')
                ->with('success', 'Ticket completado y Formato A guardado 🫡');
        }

        return redirect()->route('admin.formatos.index')
            ->with('success', 'Formato A guardado correctamente 🫡');
    });
}


 public function storeB(Request $request)
{
    try {
        $data = $request->validate([
            'id_departamento' => 'required|exists:departamentos,id_departamento',

            'subtipo' => 'required|string',
            'descripcion_servicio' => 'nullable|string',
            'equipo' => 'nullable|string',
            'marca' => 'nullable|string',
            'modelo' => 'nullable|string',
            'numero_inventario' => 'nullable|string',
            'numero_serie' => 'nullable|string',
            'procesador' => 'nullable|string',
            'ram' => 'nullable|string',
            'disco_duro' => 'nullable|string',
            'sistema_operativo' => 'nullable|string',

            'tipo_servicio' => 'nullable|in:Preventivo,Correctivo,Instalación,Corrección,Diagnóstico',
            'diagnostico' => 'nullable|string',
            'origen_falla' => 'nullable|in:Desgaste natural,Mala operación,Otro',
            'trabajo_realizado' => 'nullable|string',
            'conclusion_servicio' => 'nullable|string',

            'detalle_realizado' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'firma_usuario' => 'nullable|string',
            'firma_tecnico' => 'nullable|string',
            'firma_jefe_area' => 'nullable|string',

            // Materiales
            'materiales' => 'nullable|array',
            'materiales.*.id_material' => 'nullable|integer|exists:catalogo_materiales,id_material',
            'materiales.*.cantidad' => 'nullable|numeric|min:1',

            // ✅ vienen desde tickets (hidden)
            'id_servicio' => 'nullable|integer',
            'id_ticket'   => 'nullable|integer',
        ]);

        $idServicioFromRequest = $data['id_servicio'] ?? null;
        $idTicketFromRequest   = $data['id_ticket'] ?? null;

        return DB::transaction(function () use ($data, $idServicioFromRequest, $idTicketFromRequest) {

            $idServicio = $this->servicios->obtenerOCrearServicio(
                $idServicioFromRequest,
                'B',
                (int) $data['id_departamento']
            );

            DB::table('formato_b')->insert([
                'id_servicio'          => $idServicio,
                'subtipo'              => $data['subtipo'],
                'descripcion_servicio' => $data['descripcion_servicio'] ?? null,
                'equipo'               => $data['equipo'] ?? null,
                'marca'                => $data['marca'] ?? null,
                'modelo'               => $data['modelo'] ?? null,
                'numero_inventario'    => $data['numero_inventario'] ?? null,
                'numero_serie'         => $data['numero_serie'] ?? null,
                'procesador'           => $data['procesador'] ?? null,
                'ram'                  => $data['ram'] ?? null,
                'disco_duro'           => $data['disco_duro'] ?? null,
                'sistema_operativo'    => $data['sistema_operativo'] ?? null,

                'tipo_servicio'        => $data['tipo_servicio'] ?? null,
                'diagnostico'          => $data['diagnostico'] ?? null,
                'origen_falla'         => $data['origen_falla'] ?? null,
                'trabajo_realizado'    => $data['trabajo_realizado'] ?? null,
                'conclusion_servicio'  => $data['conclusion_servicio'] ?? null,

                'detalle_realizado'    => $data['detalle_realizado'] ?? null,
                'observaciones'        => $data['observaciones'] ?? null,
                'firma_usuario'        => $data['firma_usuario'] ?? null,
                'firma_tecnico'        => $data['firma_tecnico'] ?? (Auth::user()->usuario->nombre ?? Auth::user()->name),
                'firma_jefe_area'      => $data['firma_jefe_area'] ?? 'Jefe de Área',
                'created_at'           => now(),
                'updated_at'           => now(),
            ]);

            //  Materiales (tu lógica, encapsulada)
            $this->materiales->guardarMaterialesUtilizados($idServicio, $data['materiales'] ?? null);

            // Completar ticket por id_ticket
            if ($idTicketFromRequest) {
                $this->servicios->completarTicketPorId((int)$idTicketFromRequest, (int)$idServicio);

                return redirect()->route('admin.tickets.index')
                    ->with('success', 'Ticket completado y Formato B guardado 🫡');
            }

            return redirect()->route('admin.formatos.index')
                ->with('success', 'Formato B guardado correctamente 🫡');
        });

    } catch (\Throwable $e) {
        \Log::error('Error en storeB: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        dd('Error en storeB:', $e->getMessage());
    }
}



// =============================
// GUARDAR FORMATO C (Redes / Telefonía)
// =============================
public function storeC(Request $request)
{
    $data = $request->validate([
        'id_departamento' => 'required|exists:departamentos,id_departamento',

        'descripcion_servicio' => 'nullable|string',
        'tipo_red' => 'required|in:Red,Telefonía',
        'tipo_servicio' => 'required|in:Preventivo,Correctivo,Configuracion',
        'diagnostico' => 'nullable|string',
        'origen_falla' => 'nullable|in:Desgaste natural,Mala operación,Otro',
        'trabajo_realizado' => 'nullable|string',
        'detalle_realizado' => 'nullable|string',
        'firma_usuario' => 'nullable|string',
        'firma_tecnico' => 'nullable|string',
        'firma_jefe_area' => 'nullable|string',
        'observaciones' => 'nullable|string',

        'materiales' => 'nullable|array',
        'materiales.*.id_material' => 'nullable|integer|exists:catalogo_materiales,id_material',
        'materiales.*.cantidad' => 'nullable|numeric|min:1',

        // ✅ tickets
        'id_servicio' => 'nullable|integer',
        'id_ticket'   => 'nullable|integer',
    ]);

    $idServicioFromRequest = $data['id_servicio'] ?? null;
    $idTicketFromRequest   = $data['id_ticket'] ?? null;

    return DB::transaction(function () use ($data, $idServicioFromRequest, $idTicketFromRequest) {

        $idServicio = $this->servicios->obtenerOCrearServicio(
            $idServicioFromRequest,
            'C',
            (int) $data['id_departamento']
        );

        DB::table('formato_c')->insert([
            'id_servicio' => $idServicio,
            'descripcion_servicio' => $data['descripcion_servicio'] ?? null,
            'tipo_red' => $data['tipo_red'],
            'tipo_servicio' => $data['tipo_servicio'],
            'diagnostico' => $data['diagnostico'] ?? null,
            'origen_falla' => $data['origen_falla'] ?? null,
            'trabajo_realizado' => $data['trabajo_realizado'] ?? null,
            'detalle_realizado' => $data['detalle_realizado'] ?? null,
            'firma_usuario' => $data['firma_usuario'] ?? null,
            'firma_tecnico' => $data['firma_tecnico'] ?? (Auth::user()->usuario->nombre ?? Auth::user()->name),
            'firma_jefe_area' => $data['firma_jefe_area'] ?? 'Jefe de Área',
            'observaciones' => $data['observaciones'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->materiales->guardarMaterialesUtilizados($idServicio, $data['materiales'] ?? null);

        if ($idTicketFromRequest) {
            $this->servicios->completarTicketPorId((int)$idTicketFromRequest, (int)$idServicio);

            return redirect()->route('admin.tickets.index')
                ->with('success', 'Ticket completado y Formato C guardado 🫡');
        }

        return redirect()->route('admin.formatos.index')
            ->with('success', 'Formato C guardado correctamente 🫡');
    });
}

// =============================
// GUARDAR FORMATO D
// =============================
public function storeD(Request $request)
{
    $data = $request->validate([
        'id_departamento' => 'required|exists:departamentos,id_departamento',
        'fecha' => 'nullable|date',
        'equipo' => 'nullable|string',
        'marca' => 'nullable|string',
        'modelo' => 'nullable|string',
        'serie' => 'nullable|string',
        'diagnostico' => 'nullable|string',
        'mantenimiento_realizado' => 'nullable|string',
        'observaciones' => 'nullable|string',
        'firma_usuario' => 'nullable|string',
        'firma_tecnico' => 'nullable|string',
        'firma_jefe_area' => 'nullable|string',

        // ✅ tickets
        'id_servicio' => 'nullable|integer',
        'id_ticket'   => 'nullable|integer',
    ]);

    $idServicioFromRequest = $data['id_servicio'] ?? null;
    $idTicketFromRequest   = $data['id_ticket'] ?? null;

    return DB::transaction(function () use ($data, $idServicioFromRequest, $idTicketFromRequest) {

        $idServicio = $this->servicios->obtenerOCrearServicio(
            $idServicioFromRequest,
            'D',
            (int) $data['id_departamento']
        );

        DB::table('formato_d')->insert([
            'id_servicio' => $idServicio,
            'fecha' => $data['fecha'] ?? now()->format('Y-m-d'),
            'equipo' => $data['equipo'] ?? null,
            'marca' => $data['marca'] ?? null,
            'modelo' => $data['modelo'] ?? null,
            'serie' => $data['serie'] ?? null,
            'diagnostico' => $data['diagnostico'] ?? null,
            'mantenimiento_realizado' => $data['mantenimiento_realizado'] ?? null,
            'observaciones' => $data['observaciones'] ?? null,
            'firma_usuario' => $data['firma_usuario'] ?? null,
            'firma_tecnico' => $data['firma_tecnico'] ?? (Auth::user()->usuario->nombre ?? Auth::user()->name),
            'firma_jefe_area' => $data['firma_jefe_area'] ?? 'Jefe de Área',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($idTicketFromRequest) {
            $this->servicios->completarTicketPorId((int)$idTicketFromRequest, (int)$idServicio);

            return redirect()->route('admin.tickets.index')
                ->with('success', 'Ticket completado y Formato D guardado ✅');
        }

        return redirect()->route('admin.formatos.index')
            ->with('success', 'Formato D guardado correctamente 🫡');
    });
}

// =============================
// PREVIEW FORMATO A
// =============================
public function previewA($id)
{
    $servicio = DB::table('servicios')
        ->leftJoin('formato_a', 'formato_a.id_servicio', '=', 'servicios.id_servicio')
        ->select(
            'servicios.*',
            'formato_a.peticion',
            'formato_a.trabajo_realizado',
            'formato_a.subtipo',
            'formato_a.tipo_atencion',
            'formato_a.tipo_servicio',

            'formato_a.detalle_realizado',
            'formato_a.conclusion_servicio',
            'formato_a.observaciones',
            'formato_a.firma_usuario',
            'formato_a.firma_tecnico',
            'formato_a.firma_jefe_area'
        )
        ->where('servicios.id_servicio', $id)
        ->first();
            $departamentos = Departamento::where('activo', 1)->get();


    if (!$servicio) {
        return redirect()->route('admin.formatos.index')
            ->with('error', 'Formato A no encontrado.');
    }

    return view('admin.formatos.preview.preview_a', compact('servicio' , 'departamentos'));
}


//preview formato b

public function previewB($id)
{
    // Obtener datos principales del servicio + formato B
    $servicio = DB::table('servicios')
        ->leftJoin('formato_b', 'formato_b.id_servicio', '=', 'servicios.id_servicio')
        ->where('servicios.id_servicio', $id)
        ->select(
            'servicios.*',
            'formato_b.subtipo',
            'formato_b.descripcion_servicio',
            'formato_b.equipo',
            'formato_b.marca',
            'formato_b.modelo',
            'formato_b.procesador',
            'formato_b.ram',
            'formato_b.disco_duro',
            'formato_b.sistema_operativo',
            'formato_b.numero_serie',
            'formato_b.numero_inventario',
            'formato_b.diagnostico',
            'formato_b.origen_falla',
            'formato_b.trabajo_realizado',
            'formato_b.conclusion_servicio',
            'formato_b.detalle_realizado',
            'formato_b.observaciones',
            'formato_b.firma_usuario',
            'formato_b.firma_tecnico',
            'formato_b.firma_jefe_area'
        )
        ->first();

                    $departamentos = Departamento::where('activo', 1)->get();


    if (!$servicio) {
        return redirect()->route('admin.formatos.index')
            ->with('error', 'Formato B no encontrado.');
            
    }

    // materiales usados
    $materiales = DB::table('materiales_utilizados')
        ->join('catalogo_materiales', 'catalogo_materiales.id_material', '=', 'materiales_utilizados.id_material')
        ->where('materiales_utilizados.id_servicio', $id)
        ->select(
            'materiales_utilizados.id_material',
            'catalogo_materiales.nombre',
            'materiales_utilizados.cantidad'
        )
        ->get();

    // catalogo completo-agregar nuevos
    $catalogo_materiales = DB::table('catalogo_materiales')->get();

    return view('admin.formatos.preview.preview_b',
        compact('servicio', 'materiales', 'catalogo_materiales' , 'departamentos'));
}

// preview formato c
public function previewC($id)
{
    $servicio = DB::table('servicios')
        ->leftJoin('formato_c', 'formato_c.id_servicio', '=', 'servicios.id_servicio')
        ->where('servicios.id_servicio', $id)
        ->select(
            'servicios.*',
            'formato_c.tipo_red',
            'formato_c.tipo_servicio',
            'formato_c.descripcion_servicio',
            'formato_c.diagnostico',
            'formato_c.origen_falla',
            'formato_c.trabajo_realizado',
            'formato_c.detalle_realizado',
            'formato_c.observaciones',
            'formato_c.firma_usuario',
            'formato_c.firma_tecnico',
            'formato_c.firma_jefe_area'
        )
        ->first();
            $departamentos = Departamento::where('activo', 1)->get();

    if (!$servicio) {
        return redirect()->route('admin.formatos.index')->with('error', 'Formato C no encontrado.');
    }

    // materiales usados
    $materiales = DB::table('materiales_utilizados')
        ->join('catalogo_materiales', 'catalogo_materiales.id_material', '=', 'materiales_utilizados.id_material')
        ->where('materiales_utilizados.id_servicio', $id)
        ->select(
            'materiales_utilizados.id_material',
            'catalogo_materiales.nombre',
            'materiales_utilizados.cantidad'
        )
        ->get();

    // catalogo materiales
    $catalogo_materiales = DB::table('catalogo_materiales')->get();

    return view('admin.formatos.preview.preview_c',
        compact('servicio', 'materiales', 'catalogo_materiales', 'departamentos'));
}


public function previewD($id)
{
    $servicio = DB::table('servicios')
        ->leftJoin('formato_d', 'formato_d.id_servicio', '=', 'servicios.id_servicio')
        ->where('servicios.id_servicio', $id)
        ->select(
            'servicios.*',
            'formato_d.fecha',
            'formato_d.equipo',
            'formato_d.marca',
            'formato_d.modelo',
            'formato_d.serie',

            //  CAMPOS PARA EL PREVIEW

            'formato_d.observaciones',
            'formato_d.otorgante',
            'formato_d.receptor',
            'formato_d.firma_jefe_area'
        )
        ->first();
                    $departamentos = Departamento::where('activo', 1)->get();


    if (!$servicio) {
        return redirect()->route('admin.formatos.index')->with('error', 'Formato D no encontrado.');
    }

    return view('admin.formatos.preview.preview_d', compact('servicio' , 'departamentos'));
}



// =============================
// GENERAR PDF FORMATO A
// =============================

public function generarPDFA($id)
{
    $servicio = DB::table('servicios')
        ->leftJoin('formato_a', 'formato_a.id_servicio', '=', 'servicios.id_servicio')
        ->select(
            'servicios.*',
            'formato_a.peticion',
            'formato_a.trabajo_realizado',
            'formato_a.subtipo',
            'formato_a.tipo_atencion',
            'formato_a.tipo_servicio',
            
        
            'formato_a.detalle_realizado',
            'formato_a.conclusion_servicio',
            'formato_a.observaciones',
            'formato_a.firma_usuario',
            'formato_a.firma_tecnico',
            'formato_a.firma_jefe_area'
        )
        ->where('servicios.id_servicio', $id)
        ->first();

            $departamentos = Departamento::where('activo', 1)->get();


    if (!$servicio) {
        return redirect()->route('admin.formatos.index')
            ->with('error', 'Formato A no encontrado.');
    }

    $pdf = Pdf::loadView('admin.formatos.pdfs.pdf_formato_a', compact('servicio' , 'departamentos'))
        ->setPaper('letter', 'portrait');

    $nombre = 'FormatoA_' . ($servicio->folio ?? 'sin_folio') . '.pdf';

    // Muestra el PDF en navegador (stream)
    return $pdf->stream($nombre);
}

//generar pdf formato b

public function generarPDFB($id)
{
    $servicio = DB::table('servicios')
        ->leftJoin('formato_b', 'formato_b.id_servicio', '=', 'servicios.id_servicio')
        ->where('servicios.id_servicio', $id)
        ->select('servicios.*','formato_b.*')
        ->first();

         $departamentos = Departamento::where('activo', 1)->get();


    if (!$servicio) {
        return redirect()->route('admin.formatos.index')->with('error', 'Formato B no encontrado.');
    }

    $materiales = DB::table('materiales_utilizados')
        ->join('catalogo_materiales', 'catalogo_materiales.id_material', '=', 'materiales_utilizados.id_material')
        ->where('materiales_utilizados.id_servicio', $id)
        ->select('catalogo_materiales.nombre', 'materiales_utilizados.cantidad')
        ->get();

    $pdf = Pdf::loadView('admin.formatos.pdfs.pdf_formato_b', compact('servicio','materiales', 'departamentos'))
        ->setPaper('letter','portrait');

    return $pdf->stream('FormatoB_'.$servicio->folio.'.pdf');
}

//pdf formato c

public function generarPDFC($id)
{
    $servicio = DB::table('servicios')
        ->leftJoin('formato_c', 'formato_c.id_servicio', '=', 'servicios.id_servicio')
        ->where('servicios.id_servicio', $id)
        ->select('servicios.*','formato_c.*')
        ->first();

         $departamentos = Departamento::where('activo', 1)->get();


    if (!$servicio) {
        return redirect()->route('admin.formatos.index')->with('error', 'Formato C no encontrado.');
    }

    $materiales = DB::table('materiales_utilizados')
        ->join('catalogo_materiales', 'catalogo_materiales.id_material', '=', 'materiales_utilizados.id_material')
        ->where('materiales_utilizados.id_servicio', $id)
        ->select('catalogo_materiales.nombre', 'materiales_utilizados.cantidad')
        ->get();

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.formatos.pdfs.pdf_formato_c', compact('servicio', 'materiales' , 'departamentos'))
        ->setPaper('letter','portrait');

    return $pdf->stream('FormatoC_'.$servicio->folio.'.pdf');
}


//pdf formato d
public function generarPDFD($id)
{
    $servicio = DB::table('servicios')
        ->leftJoin('formato_d', 'formato_d.id_servicio', '=', 'servicios.id_servicio')
        ->where('servicios.id_servicio', $id)
        ->select('servicios.*','formato_d.*')
        ->first();
            $departamentos = Departamento::where('activo', 1)->get();

    if (!$servicio) {
        return redirect()->route('admin.formatos.index')->with('error', 'Formato D no encontrado.');
    }

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.formatos.pdfs.pdf_formato_d', compact('servicio', 'departamentos'))
        ->setPaper('letter','portrait');

    return $pdf->stream('FormatoD_'.$servicio->folio.'.pdf');
}



//editar FORMATO
public function edit($tipo, $id)
{
    $servicio = DB::table('servicios')->where('id_servicio', $id)->first();

    if (!$servicio) abort(404);

    switch (strtoupper($tipo)) {

        case 'A':
            $formato = DB::table('formato_a')->where('id_servicio', $id)->first();
            return view('admin.formatos.edit.edit_a', compact('servicio', 'formato'));

        case 'B':
            $formato = DB::table('formato_b')->where('id_servicio', $id)->first();

           $materiales = DB::table('materiales_utilizados')
    ->join('catalogo_materiales', 'catalogo_materiales.id_material', '=', 'materiales_utilizados.id_material')
    ->where('materiales_utilizados.id_servicio', $id)
    ->select(
        'materiales_utilizados.id_material',   // 👈 NECESARIO
        'catalogo_materiales.nombre',
        'materiales_utilizados.cantidad'
    )
    ->get();

            return view('admin.formatos.edit.edit_b', compact('servicio', 'formato', 'materiales'));

        case 'C':
            $formato = DB::table('formato_c')->where('id_servicio', $id)->first();

            $materiales = DB::table('materiales_utilizados')
                ->where('id_servicio', $id)
                ->get();

            return view('admin.formatos.edit.edit_c', compact('servicio', 'formato', 'materiales'));

        case 'D':
            $formato = DB::table('formato_d')->where('id_servicio', $id)->first();
            return view('admin.formatos.edit.edit_d', compact('servicio', 'formato'));

        default:
            abort(404);
    }
}

// ============================= 

// ACTUALIZAR FORMATO
public function update(Request $request, $tipo, $id)
{
    $data = $request->except('_token');

    switch (strtoupper($tipo)) {

        case 'A':

            // 1. ACTUALIZAR DEPARTAMENTO (tabla servicios)
            if (isset($data['id_departamento'])) {
                DB::table('servicios')
                    ->where('id_servicio', $id)
                    ->update([
                        'id_departamento' => $data['id_departamento'],
                        'updated_at' => now(),
                    ]);

                unset($data['id_departamento']); // 🔥 CLAVE
            }

            // 2. ACTUALIZAR FORMATO A
            DB::table('formato_a')
                ->where('id_servicio', $id)
                ->update($data);

            // ✅ HOOK: si este servicio pertenece a un ticket, marcar como completado
            DB::table('tickets')
                ->where('id_servicio', $id)
                ->update([
                    'estado' => 'completado',
                    'updated_at' => now(),
                ]);

            break;

        case 'B':

            // 1. ACTUALIZAR DEPARTAMENTO (SERVICIOS)
            if (isset($data['id_departamento'])) {
                DB::table('servicios')
                    ->where('id_servicio', $id)
                    ->update([
                        'id_departamento' => $data['id_departamento'],
                        'updated_at' => now(),
                    ]);

                unset($data['id_departamento']); // 👈 MUY IMPORTANTE
            }

            // 2. QUITAR MATERIALES DEL UPDATE
            if (isset($data['materiales'])) {
                unset($data['materiales']);
            }

            // 3. ACTUALIZAR FORMATO B
            DB::table('formato_b')
                ->where('id_servicio', $id)
                ->update($data);

            // 4. ACTUALIZAR MATERIALES (NO SE TOCA TU LÓGICA)
            if ($request->has('materiales')) {

                DB::table('materiales_utilizados')
                    ->where('id_servicio', $id)
                    ->delete();

                foreach ($request->materiales as $m) {
                    if (!empty($m['id_material'])) {
                        DB::table('materiales_utilizados')->insert([
                            'id_servicio' => $id,
                            'id_material' => $m['id_material'],
                            'cantidad'    => $m['cantidad'] ?? 1,
                        ]);
                    }
                }
            }

            // ✅ HOOK: si este servicio pertenece a un ticket, marcar como completado
            DB::table('tickets')
                ->where('id_servicio', $id)
                ->update([
                    'estado' => 'completado',
                    'updated_at' => now(),
                ]);

            break;

        case 'C':

            // 1. ACTUALIZAR DEPARTAMENTO (SERVICIOS)
            if (isset($data['id_departamento'])) {
                DB::table('servicios')
                    ->where('id_servicio', $id)
                    ->update([
                        'id_departamento' => $data['id_departamento'],
                        'updated_at' => now(),
                    ]);

                unset($data['id_departamento']);
            }

            // 2. QUITAR MATERIALES
            if (isset($data['materiales'])) {
                unset($data['materiales']);
            }

            // 3. ACTUALIZAR FORMATO C
            DB::table('formato_c')
                ->where('id_servicio', $id)
                ->update($data);

            // 4. ACTUALIZAR MATERIALES (NO SE TOCA TU LÓGICA)
            if ($request->has('materiales')) {

                DB::table('materiales_utilizados')
                    ->where('id_servicio', $id)
                    ->delete();

                foreach ($request->materiales as $m) {
                    if (!empty($m['id_material'])) {
                        DB::table('materiales_utilizados')->insert([
                            'id_servicio' => $id,
                            'id_material' => $m['id_material'],
                            'cantidad'    => $m['cantidad'] ?? 1
                        ]);
                    }
                }
            }

            // ✅ HOOK: si este servicio pertenece a un ticket, marcar como completado
            DB::table('tickets')
                ->where('id_servicio', $id)
                ->update([
                    'estado' => 'completado',
                    'updated_at' => now(),
                ]);

            break;

        case 'D':

            DB::table('formato_d')
                ->where('id_servicio', $id)
                ->update($data);

            // ✅ HOOK: si este servicio pertenece a un ticket, marcar como completado
            DB::table('tickets')
                ->where('id_servicio', $id)
                ->update([
                    'estado' => 'completado',
                    'updated_at' => now(),
                ]);

            break;

        default:
            abort(404);
    }

    return redirect()->back()->with('success', 'Formato actualizado correctamente.');
}
// =============================

// REPORTE GENERAL PDF
// =============================
public function reporteGeneral(Request $request)
{
   $tipo = $request->input('tipo');
    $usuario = $request->input('usuario');
    $fecha = $request->input('fecha');

    $cuenta = Auth::user();

    // Base principal
    $query = DB::table('servicios')
        ->leftJoin('usuarios', 'usuarios.id_usuario', '=', 'servicios.id_usuario')
        ->select(
            'servicios.id_servicio',
            'servicios.folio',
            'servicios.fecha',
            'servicios.tipo_formato',
            'usuarios.nombre as usuario'
        );

    //Si el usuario NO es admin, limitar a sus registros
    if (!$cuenta->isAdmin()) {
        $query->where('servicios.id_usuario', $cuenta->id_usuario);
    }

    //  Aplicar filtros opcionales
    if ($tipo) $query->where('servicios.tipo_formato', $tipo);
    if ($usuario && $cuenta->isAdmin()) // Solo admins pueden filtrar por usuario
        $query->where('usuarios.nombre', 'like', "%$usuario%");
    if ($fecha) $query->whereDate('servicios.fecha', $fecha);

    $servicios = $query->get();

    // Unir campos adicionales según tipo
    $formatos = collect();
    foreach ($servicios as $s) {
        $detalle = match ($s->tipo_formato) {
            'A' => DB::table('formato_a')->where('id_servicio', $s->id_servicio)->first(),
            'B' => DB::table('formato_b')->where('id_servicio', $s->id_servicio)->first(),
            'C' => DB::table('formato_c')->where('id_servicio', $s->id_servicio)->first(),
            'D' => DB::table('formato_d')->where('id_servicio', $s->id_servicio)->first(),
            default => null,
        };
        $formatos->push((object) array_merge((array)$s, (array)($detalle ?? [])));
    }

    if ($formatos->isEmpty()) {
        return back()->with('error', 'No hay registros para generar el reporte.');
    }

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
        'admin.formatos.pdfs.pdf_reporte_general',
        compact('formatos', 'tipo', 'usuario', 'fecha')
    )->setPaper('letter', 'portrait');

    $nombre = 'Reporte_Formatos_' . ($cuenta->isAdmin() ? 'General' : $cuenta->usuario->nombre) . '_' . now()->format('Ymd_His') . '.pdf';

    return $pdf->stream($nombre);

}


}








