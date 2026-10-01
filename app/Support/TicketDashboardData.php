<?php

namespace App\Support;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TicketDashboardData
{
    public static function forAccount(int $accountId, string $scope, int $year): array
    {
        if (!in_array($scope, ['asignado_a', 'creado_por'], true)) {
            throw new \InvalidArgumentException('Alcance de tickets no válido.');
        }

        $query = DB::table('tickets')->where($scope, $accountId);
        $firstDate = (clone $query)->min('created_at');
        $firstYear = $firstDate ? Carbon::parse($firstDate)->year : $year;
        $monthly = [
            $year - 1 => array_fill(1, 12, 0),
            $year => array_fill(1, 12, 0),
        ];

        foreach ((clone $query)
            ->whereBetween('created_at', [Carbon::create($year - 1, 1, 1)->startOfDay(), Carbon::create($year, 12, 31)->endOfDay()])
            ->select('created_at')
            ->cursor() as $ticket) {
            $date = Carbon::parse($ticket->created_at);
            $monthly[$date->year][$date->month]++;
        }

        return [
            'yearMin' => max(2000, min($year, $firstYear)),
            'current' => array_values($monthly[$year]),
            'previous' => array_values($monthly[$year - 1]),
        ];
    }
}
