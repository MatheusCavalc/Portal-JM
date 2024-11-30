<?php

namespace App\Services;

use App\Models\Invoicing;
use Carbon\Carbon;

class InvoicingService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {

    }

    public function filterSales()
    {
        // Definir os meses e anos padrão, se não forem fornecidos
        $currentMonth = $currentMonth ?? Carbon::now()->month;
        $currentYear = $currentYear ?? Carbon::now()->year;
        $previousMonth = $previousMonth ?? Carbon::now()->subMonth()->month;
        $previousYear = $previousYear ?? Carbon::now()->subMonth()->year;

        // Buscar intervalos de datas
        $dateIntervals = Invoicing::select('initial_date', 'final_date')
            ->whereMonth('final_date', $currentMonth)
            ->whereYear('final_date', $currentYear)
            ->distinct()
            ->orderBy('final_date')
            ->get();

        if ($dateIntervals->isEmpty()) {
            $dateIntervals = Invoicing::select('initial_date', 'final_date')
                ->whereMonth('final_date', $previousMonth)
                ->whereYear('final_date', $previousYear)
                ->distinct()
                ->orderBy('final_date')
                ->get();
        }

        // Buscar vendas
        $sales = Invoicing::with('seller')
            ->select('seller_id', 'initial_date', 'final_date', 'value')
            ->whereIn('initial_date', $dateIntervals->pluck('initial_date'))
            ->get();

        // Organizar dados
        $data = [];
        $totalsByInterval = [];
        $grandTotal = 0;

        foreach ($sales as $sale) {
            $sellerName = $sale->seller->name;
            $key = "{$sale->initial_date} a {$sale->final_date}";

            $data[$sellerName][$key] = $sale->value;

            if (!isset($data[$sellerName]['total'])) {
                $data[$sellerName]['total'] = 0;
            }
            $data[$sellerName]['total'] += $sale->value;

            if (!isset($totalsByInterval[$key])) {
                $totalsByInterval[$key] = 0;
            }
            $totalsByInterval[$key] += $sale->value;

            $grandTotal += $sale->value;
        }

        // Garantir que todas as colunas estejam presentes para cada vendedor
        foreach ($data as $sellerName => $intervals) {
            foreach ($dateIntervals as $interval) {
                $key = "{$interval->initial_date} a {$interval->final_date}";
                if (!isset($data[$sellerName][$key])) {
                    $data[$sellerName][$key] = 0;
                }
            }
        }

        return [
            'data' => $data,
            'totalsByInterval' => $totalsByInterval,
            'grandTotal' => $grandTotal,
            'dateIntervals' => $dateIntervals
        ];
    }
}
