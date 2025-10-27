<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use \Barryvdh\DomPDF\Facade\Pdf;


class FormatoController extends Controller
{
    // =============================
    // INDEX - LISTA DE FORMATOS
    // =============================
    public function index(Request $request)
    {
        // 🔍 Filtros desde el formulario
        $tipo = $request->input('tipo');
        $usuario = $request->input('usuario');
        $fecha = $request->input('fecha');

        // Consulta base
        $query = DB::table('servicios')
            ->leftJoin('usuarios', 'usuarios.id_usuario', '=', 'servicios.id_usuario')
            ->select(
                'servicios.id_servicio',            // 👈 Campo clave para los botones
                'servicios.tipo_formato as tipo',
                'servicios.fecha',
                'usuarios.nombre'
            )
            ->orderByDesc('servicios.id_servicio');

        // Aplicar filtros dinámicos
        if (!empty($tipo)) {
            $query->where('servicios.tipo_formato', $tipo);
        }

        if (!empty($usuario)) {
            $query->where('usuarios.nombre', 'like', "%$usuario%");
        }

        if (!empty($fecha)) {
            $query->whereDate('servicios.fecha', $fecha);
        }

        // Ejecutar consulta final
        $formatos = $query->get();

        // Retornar vista con filtros
        return view('admin.formatos.index', compact('formatos', 'tipo', 'usuario', 'fecha'));
    }

    // =============================
    // CREAR NUEVO FORMATO (vista)
    // =============================
    public function create()
    {
        return view('admin.formatos.create');
    }


    public function formatoA()
    {
        return view('admin.formatos.formato_a');
    }

    public function formatoB()
    {
        return view('admin.formatos.formato_b');
    }

    public function formatoC()
    {
        return view('admin.formatos.formato_c');
    }

    public function formatoD()
    {
        return view('admin.formatos.formato_d');
    }   
    public function storeA(Request $request)
    {
 $data = $request->validate([
    'subtipo' => 'required|string',
    'tipo_atencion' => 'nullable|string',
    'peticion' => 'nullable|string',
    'tipo_servicio' => 'nullable|string',
    'trabajo_realizado' => 'nullable|string',
    'conclusion_servicio' => 'nullable|string',
    'detalle_realizado' => 'nullable|string',
    'firma_usuario' => 'nullable|string',
    'firma_tecnico' => 'nullable|string',
    'firma_jefe_area' => 'nullable|string',
    'observaciones' => 'nullable|string',
]);
    $idServicio = DB::table('servicios')->insertGetId([
        'folio' => 'A-' . time(),
        'fecha' => now()->format('Y-m-d'),
        'id_usuario' => Auth::id(), // 👈 Nuevo campo

        'tipo_formato' => 'A',
        'created_at' => now(),
    ]);


DB::table('formato_a')->insert([
    'id_servicio' => $idServicio,
    'subtipo' => $data['subtipo'],
    'tipo_atencion' => $data['tipo_atencion'] ?? null,
    'peticion' => $data['peticion'] ?? null,
    'tipo_servicio' => $data['tipo_servicio'] ?? null,
    'trabajo_realizado' => $data['trabajo_realizado'] ?? null,
    'conclusion_servicio' => $data['conclusion_servicio'] ?? null,
    'detalle_realizado' => $data['detalle_realizado'] ?? null,
    'firma_usuario' => $data['firma_usuario'] ?? null,
    'firma_tecnico' => $data['firma_tecnico'] ?? null,
    'firma_jefe_area' => $data['firma_jefe_area'] ?? null,
    'observaciones' => $data['observaciones'] ?? null,
]);;

        return redirect()->route('admin.formatos.index')->with('success', 'Formato A guardado correctamente ✅');
    }

public function storeB(Request $request)
{
    $data = $request->validate([
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

        'tipo_servicio' => 'nullable|in: Preventivo,Correctivo,Instalación, Corrección, Diagnóstico',
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
    ]);

    $idServicio = DB::table('servicios')->insertGetId([
        'folio' => 'B-' . time(),
        'fecha' => now()->format('Y-m-d'),
        'id_usuario' => Auth::id(), // 👈 Nuevo campo

        'tipo_formato' => 'B',
        'created_at' => now(),
    ]);

    $insertData = [
        'id_servicio'       => $idServicio,
        'subtipo'           => $data['subtipo'],
        'descripcion_servicio' => $data['descripcion_servicio'] ?? null,
        'equipo' => $data['equipo'] ?? null,
        'marca' => $data['marca'] ?? null,
        'modelo' => $data['modelo'] ?? null,
        'numero_inventario' => $data['numero_inventario'] ?? null,
        'numero_serie' => $data['numero_serie'] ?? null,
        'procesador' => $data['procesador'] ?? null,
        'ram' => $data['ram'] ?? null,
        'disco_duro' => $data['disco_duro'] ?? null,
        'sistema_operativo' => $data['sistema_operativo'] ?? null,

        'tipo_servicio' => $data['tipo_servicio'] ?? null,
        'diagnostico' => $data['diagnostico'] ?? null,
        'origen_falla' => $data['origen_falla'] ?? null,
        'trabajo_realizado' => $data['trabajo_realizado'] ?? null,
        'conclusion_servicio' => $data['conclusion_servicio'] ?? null,

        'detalle_realizado' => $data['detalle_realizado'] ?? null,
        'observaciones'     => $data['observaciones'] ?? null,
        'firma_usuario'     => $data['firma_usuario'] ?? null,
        'firma_tecnico'     => $data['firma_tecnico'] ?? (Auth::user()->usuario->nombre ?? Auth::user()->name),
        'firma_jefe_area'   => $data['firma_jefe_area'] ?? 'Jefe de Área',
        'created_at'        => now(),
        'updated_at'        => now(),
    ];

    DB::table('formato_b')->insert($insertData);

    if (!empty($data['materiales'])) {
        foreach ($data['materiales'] as $mat) {
            if (!empty($mat['id_material'])) {
                DB::table('materiales_utilizados')->insert([
                    'id_servicio' => $idServicio,
                    'id_material' => $mat['id_material'],
                    'cantidad'    => $mat['cantidad'] ?? 1,
                ]);
            }
        }
    }

    return redirect()->route('admin.formatos.index')->with('success', 'Formato B guardado correctamente ✅');
}




// =============================
// GUARDAR FORMATO C (Redes / Telefonía)
// =============================
public function storeC(Request $request)
{
    $data = $request->validate([
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
    ]);

    $idServicio = DB::table('servicios')->insertGetId([
        'folio' => 'C-' . time(),
        'fecha' => now()->format('Y-m-d'),
        'id_usuario' => Auth::id(), // 👈 Nuevo campo

        'tipo_formato' => 'C',
        'created_at' => now(),
    ]);

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

    if (!empty($data['materiales'])) {
        foreach ($data['materiales'] as $mat) {
            if (!empty($mat['id_material'])) {
                DB::table('materiales_utilizados')->insert([
                    'id_servicio' => $idServicio,
                    'id_material' => $mat['id_material'],
                    'cantidad' => $mat['cantidad'] ?? 1,
                ]);
            }
        }
    }

    return redirect()->route('admin.formatos.index')->with('success', 'Formato C guardado correctamente ✅');
}

// =============================
// GUARDAR FORMATO D
// =============================
public function storeD(Request $request)
{
    $data = $request->validate([
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
    ]);

    $idServicio = DB::table('servicios')->insertGetId([
        'folio' => 'D-' . time(),
        'fecha' => $data['fecha'] ?? now()->format('Y-m-d'),
        'id_usuario' => Auth::id(), // 👈 Nuevo campo

        'tipo_formato' => 'D',
        'created_at' => now(),
    ]);

    DB::table('formato_d')->insert(array_merge($data, ['id_servicio' => $idServicio]));

    return redirect()->route('admin.formatos.index')->with('success', 'Formato D guardado correctamente ✅');
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
            'formato_a.conclusion_servicio',
            'formato_a.observaciones',
            'formato_a.firma_usuario',
            'formato_a.firma_tecnico',
            'formato_a.firma_jefe_area'
        )
        ->where('servicios.id_servicio', $id)
        ->first();

    if (!$servicio) {
        return redirect()->route('admin.formatos.index')
            ->with('error', 'Formato A no encontrado.');
    }

    return view('admin.formatos.preview.preview_a', compact('servicio'));
}


//preview formato b

public function previewB($id)
{
    $servicio = DB::table('servicios')
        ->leftJoin('formato_b', 'formato_b.id_servicio', '=', 'servicios.id_servicio')
        ->where('servicios.id_servicio', $id)
        ->select(
            'servicios.*',
            'formato_b.subtipo',
            'formato_b.descripcion_servicio',
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

    if (!$servicio) {
        return redirect()->route('admin.formatos.index')
            ->with('error', 'Formato B no encontrado.');
    }

    // 🧱 Materiales usados
    $materiales = DB::table('materiales_utilizados')
        ->join('catalogo_materiales', 'catalogo_materiales.id_material', '=', 'materiales_utilizados.id_material')
        ->where('materiales_utilizados.id_servicio', $id)
        ->select('catalogo_materiales.nombre', 'materiales_utilizados.cantidad')
        ->get();

    return view('admin.formatos.preview.preview_b', compact('servicio', 'materiales'));
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

    if (!$servicio) {
        return redirect()->route('admin.formatos.index')->with('error', 'Formato C no encontrado.');
    }

    $materiales = DB::table('materiales_utilizados')
        ->join('catalogo_materiales', 'catalogo_materiales.id_material', '=', 'materiales_utilizados.id_material')
        ->where('materiales_utilizados.id_servicio', $id)
        ->select('catalogo_materiales.nombre', 'materiales_utilizados.cantidad')
        ->get();

    return view('admin.formatos.preview.preview_c', compact('servicio', 'materiales'));
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
            'formato_d.otorgante',
            'formato_d.receptor',
            'formato_d.firma_jefe_area',
            'formato_d.observaciones'
        )
        ->first();

    if (!$servicio) {
        return redirect()->route('admin.formatos.index')->with('error', 'Formato D no encontrado.');
    }

    return view('admin.formatos.preview.preview_d', compact('servicio'));
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
            'formato_a.conclusion_servicio',
            'formato_a.observaciones',
            'formato_a.firma_usuario',
            'formato_a.firma_tecnico',
            'formato_a.firma_jefe_area'
        )
        ->where('servicios.id_servicio', $id)
        ->first();

    if (!$servicio) {
        return redirect()->route('admin.formatos.index')
            ->with('error', 'Formato A no encontrado.');
    }

    $pdf = Pdf::loadView('admin.formatos.pdfs.pdf_formato_a', compact('servicio'))
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

    if (!$servicio) {
        return redirect()->route('admin.formatos.index')->with('error', 'Formato B no encontrado.');
    }

    $materiales = DB::table('materiales_utilizados')
        ->join('catalogo_materiales', 'catalogo_materiales.id_material', '=', 'materiales_utilizados.id_material')
        ->where('materiales_utilizados.id_servicio', $id)
        ->select('catalogo_materiales.nombre', 'materiales_utilizados.cantidad')
        ->get();

    $pdf = Pdf::loadView('admin.formatos.pdfs.pdf_formato_b', compact('servicio','materiales'))
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

    if (!$servicio) {
        return redirect()->route('admin.formatos.index')->with('error', 'Formato C no encontrado.');
    }

    $materiales = DB::table('materiales_utilizados')
        ->join('catalogo_materiales', 'catalogo_materiales.id_material', '=', 'materiales_utilizados.id_material')
        ->where('materiales_utilizados.id_servicio', $id)
        ->select('catalogo_materiales.nombre', 'materiales_utilizados.cantidad')
        ->get();

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.formatos.pdfs.pdf_formato_c', compact('servicio', 'materiales'))
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

    if (!$servicio) {
        return redirect()->route('admin.formatos.index')->with('error', 'Formato D no encontrado.');
    }

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.formatos.pdfs.pdf_formato_d', compact('servicio'))
        ->setPaper('letter','portrait');

    return $pdf->stream('FormatoD_'.$servicio->folio.'.pdf');
}


public function reporteGeneral(Request $request)
{
    $tipo = $request->input('tipo');
    $usuario = $request->input('usuario');
    $fecha = $request->input('fecha');

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

    if ($tipo) $query->where('servicios.tipo_formato', $tipo);
    if ($usuario) $query->where('usuarios.nombre', 'like', "%$usuario%");
    if ($fecha) $query->whereDate('servicios.fecha', $fecha);

    $servicios = $query->get();

    // 👇 unir campos adicionales según tipo
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

    return $pdf->stream('Reporte_Formatos_'.now()->format('Ymd_His').'.pdf');
}


}








