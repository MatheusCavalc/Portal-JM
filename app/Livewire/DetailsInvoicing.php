<?php

namespace App\Livewire;

use App\Models\Invoicing;
use Carbon\Carbon;
use Livewire\Component;

class DetailsInvoicing extends Component
{
    public $dateIntervals = [];

    public $data = [];

    public $totalsByInterval = [];

    public $grandTotal = 0;

    public $chartData = [];

    public $months = [];

    public $currentMonth = '';

    public $previousMonth = '';

    public $filterMonth = '';

    public $currentYear = '';

    public $previousYear = '';

    public $filterInitialDate = '';

    public $filterFinalDate = '';

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

        $this->defaultFilter();
    }

    public function cleanFilters()
    {
        $this->filterMonth = '';

        $this->filterInitialDate = '';

        $this->filterFinalDate = '';

        $this->currentMonth = Carbon::now()->month;
        $this->previousMonth = Carbon::now()->subMonth()->month;

        $this->currentYear = Carbon::now()->year;
        $this->previousYear = Carbon::now()->subMonth()->year;

        $this->defaultFilter();
    }

    public function filterSales($currentMonth, $currentYear, $previousMonth = null, $previousYear = null)
    {
        $previousMonth = $previousMonth ?? $currentMonth;
        $previousYear = $previousYear ?? $currentYear;

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

    public function applyDateRangeFilter()
    {
        if (!$this->filterInitialDate || !$this->filterFinalDate) {
            return session()->flash('error', 'Por favor, selecione ambas as datas para aplicar o filtro.');
        }

        $this->filterMonth = '';

        // Validar que a data inicial não seja maior que a final
        if (Carbon::parse($this->filterInitialDate)->gt(Carbon::parse($this->filterFinalDate))) {
            return session()->flash('error', 'A data inicial não pode ser maior que a data final.');
        }

        $sales = Invoicing::with('seller')
            ->select('seller_id', 'initial_date', 'final_date', 'value')
            ->whereBetween('final_date', [$this->filterInitialDate, $this->filterFinalDate])
            ->get();

        $this->processSales($sales);
    }

    protected function processSales($sales)
    {
        $this->data = [];
        $this->totalsByInterval = [];
        $this->grandTotal = 0;

        foreach ($sales as $sale) {
            $sellerName = $sale->seller->name;
            $key = "{$sale->initial_date} a {$sale->final_date}";

            $this->data[$sellerName][$key] = $sale->value;

            if (!isset($this->data[$sellerName]['total'])) {
                $this->data[$sellerName]['total'] = 0;
            }
            $this->data[$sellerName]['total'] += $sale->value;

            if (!isset($this->totalsByInterval[$key])) {
                $this->totalsByInterval[$key] = 0;
            }
            $this->totalsByInterval[$key] += $sale->value;

            $this->grandTotal += $sale->value;
        }

        $this->dispatch('update-chart', ['data' => $this->data]);
    }

    public function defaultFilter()
    {
        $filters = $this->filterSales(
            $this->currentMonth,
            $this->currentYear,
            $this->previousMonth,
            $this->previousYear
        );

        $this->data = $filters['data'];
        $this->totalsByInterval = $filters['totalsByInterval'];
        $this->grandTotal = $filters['grandTotal'];
        $this->dateIntervals = $filters['dateIntervals'];
        $this->chartData = $this->data;

        $this->dispatch('update-chart', ['data' => $this->data]);
    }

    public function applyFilters()
    {
        if (!$this->filterMonth) {
            // Exibe mensagem de erro e sai do método
            return session()->flash('errorMonth', 'Por favor, selecione um mês para filtrar.');
        }

        $this->filterInitialDate = '';

        $this->filterFinalDate = '';

        // Atualiza os valores apenas se um mês válido for selecionado
        $this->currentMonth = $this->filterMonth;
        $this->previousMonth = $this->filterMonth;

        // Chama o filtro padrão
        $this->defaultFilter();

        // Atualiza o gráfico com os dados processados
        $this->dispatch('update-chart', ['data' => $this->data]);
    }

    public function render()
    {
        return view('livewire.details-invoicing');
    }
}
