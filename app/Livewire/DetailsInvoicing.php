<?php

namespace App\Livewire;

use App\Models\Invoicing;
use Carbon\Carbon;
use Livewire\Component;

class DetailsInvoicing extends Component
{
    public $chartData = [];

    public $months = [];

    public $currentMonth = '';

    public $previousMonth = '';

    public $filterMonth = '';

    public $currentYear = '';

    public $previousYear = '';

    public function mount()
    {
        $this->months = [
            1 => "Janeiro",
            2 => "Fevereiro",
            3 => "Março",
            4 => "Abril",
            5 => "Maio",
            6 => "Junho",
            7 => "Julho",
            8 => "Agosto",
            9 => "Setembro",
            10 => "Outubro",
            11 => "Novembro",
            12 => "Dezembro"
        ];

        $this->currentMonth = Carbon::now()->month;
        $this->previousMonth = Carbon::now()->subMonth()->month;

        $this->currentYear = Carbon::now()->year;
        $this->previousYear = Carbon::now()->subMonth()->year;
    }

    public function addFilters()
    {
        $this->currentMonth = $this->previousMonth = $this->filterMonth;

        $this->render();

        $this->dispatch('update-chart', ['data' => $this->chartData]);

        //dd($this->chartData);
    }

    public function clearFilters()
    {
        $this->currentMonth = Carbon::now()->month;
        $this->previousMonth = Carbon::now()->subMonth()->month;

        $this->currentYear = Carbon::now()->year;
        $this->previousYear = Carbon::now()->subMonth()->year;
    }

    public function render()
    {
        $dateIntervals = Invoicing::select('initial_date', 'final_date')
            ->whereMonth('final_date', $this->currentMonth)
            ->whereYear('final_date', $this->currentYear)
            ->distinct()
            ->orderBy('final_date')
            ->get();

        if ($dateIntervals->isEmpty()) {
            $dateIntervals = Invoicing::select('initial_date', 'final_date')
                ->whereMonth('final_date', $this->previousMonth)
                ->whereYear('final_date', $this->previousYear)
                ->distinct()
                ->orderBy('final_date')
                ->get();
        }

        $sales = Invoicing::with('seller')->select('seller_id', 'initial_date', 'final_date', 'value')
            ->whereIn('initial_date', $dateIntervals->pluck('initial_date'))
            ->get();

        $data = [];
        $totalsByInterval = [];
        $grandTotal = 0;

        foreach ($sales as $sale) {
            $sellerName = $sale->seller->name; // Obter o nome do vendedor
            $key = "{$sale->initial_date} a {$sale->final_date}";
            $data[$sellerName][$key] = $sale->value;

            // Incrementar a soma total do vendedor
            if (!isset($data[$sellerName]['total'])) {
                $data[$sellerName]['total'] = 0;
            }
            $data[$sellerName]['total'] += $sale->value;

            // Calcular o total por intervalo
            if (!isset($totalsByInterval[$key])) {
                $totalsByInterval[$key] = 0;
            }
            $totalsByInterval[$key] += $sale->value;

            // Calcular o total geral
            $grandTotal += $sale->value;
        }

        // Garantir que todas as colunas estejam presentes para cada vendedor
        foreach ($data as $sellerName => $intervals) {
            foreach ($dateIntervals as $interval) {
                $key = "{$interval->initial_date} a {$interval->final_date}";
                if (!isset($data[$sellerName][$key])) {
                    $data[$sellerName][$key] = 0; // Valor padrão
                }
            }
        }

        $this->chartData = $data;

        $this->dispatch('update-chart', ['data' => $data]);

        return view('livewire.details-invoicing')->with([
            'data' => $data,
            'dateIntervals' => $dateIntervals,
            'totalsByInterval' => $totalsByInterval,
            'grandTotal' => $grandTotal,
        ]);
    }
}
