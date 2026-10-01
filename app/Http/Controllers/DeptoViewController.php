<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Support\TicketDashboardData;
use Illuminate\Support\Facades\DB;

class DeptoViewController extends Controller
{
    public function dashboard(Request $request)
    {
        $yearNow = now()->year;
        $request->validate([
            'anio' => "nullable|integer|between:2000,{$yearNow}",
            'mes' => 'nullable|integer|between:0,12',
        ]);
        $year = (int) $request->input('anio', $yearNow);
        $month = $request->has('mes') ? (int) $request->input('mes') : ($year === $yearNow ? now()->month : 0);
        $months = [1 => 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        $accountId = (int) auth()->id();

        $misSolicitudes = Ticket::where('creado_por', $accountId)
            ->orderByDesc('created_at')->limit(5)->get();
        $allStatusCounts = DB::table('tickets')->where('creado_por', $accountId)
            ->select('estado', DB::raw('COUNT(*) AS total'))
            ->groupBy('estado')->pluck('total', 'estado');
        $statusQuery = DB::table('tickets')->where('creado_por', $accountId)->whereYear('created_at', $year);
        if ($month > 0) {
            $statusQuery->whereMonth('created_at', $month);
        }
        $statusCounts = $statusQuery->select('estado', DB::raw('COUNT(*) AS total'))
            ->groupBy('estado')->pluck('total', 'estado');
        $ticketActivity = TicketDashboardData::forAccount($accountId, 'creado_por', $year);

        return view('departamento.dashboard', compact(
            'misSolicitudes', 'statusCounts', 'allStatusCounts', 'ticketActivity', 'year', 'month', 'months'
        ));
    }
    
    
    }
