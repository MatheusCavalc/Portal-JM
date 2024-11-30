<?php

namespace App\Livewire;

use App\Models\Invoicing;
use App\Services\InvoicingService;
use Carbon\Carbon;
use Livewire\Component;

class DetailsInvoicing extends Component
{
    /*
    public $data = [];

    public $totalsByInterval = [];

    public $dateIntervals = [];

    public $grandTotal = 0;

    public function filterInvoices()
    {
        $initialDate = $request->input('initial_date');
        $finalDate = $request->input('final_date');
        $month = $request->input('month');

        // Adiciona o mês ao filtro
        $year = $request->input('year');

        // Adiciona o ano ao filtro
        $sellerType = $request->input('seller_type');

        // Adiciona o tipo de vendedor ao filtro
        // Se o mês e o ano forem fornecidos, usar essas datas
        if ($month && $year) {
            $initialDate = Carbon::create($year, $month, 1)->startOfMonth()->toDateString();
            $finalDate = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();
        } else {
            // Se as datas não forem fornecidas, usar o mês atual como padrão
            if (!$initialDate || !$finalDate) {
                $initialDate = Carbon::now()->startOfMonth()->toDateString();
                $finalDate = Carbon::now()->endOfMonth()->toDateString();
            }
        }
        // Ajustar as datas para representar uma semana (segunda a sexta-feira)
        $initialDate = Carbon::parse($initialDate)->startOfWeek()->toDateString();
        $finalDate = Carbon::parse($finalDate)->endOfWeek()->subDays(2)->toDateString();

        // Subtrai 2 dias para obter sexta-feira
        // Filtrar os intervalos de datas
        $this->dateIntervals = Invoicing::select('initial_date', 'final_date')
            ->whereBetween('final_date', [$initialDate, $finalDate])
            ->distinct()
            ->orderBy('final_date')
            ->get();

        // Filtrar as vendas com base nos intervalos de datas obtidos
        $salesQuery = Invoicing::with('seller')
            ->select('seller_id', 'initial_date', 'final_date', 'value')
            ->whereIn('initial_date', $this->dateIntervals->pluck('initial_date'));

        // Adicionar filtro de tipo de vendedor, se fornecido
        if ($sellerType) {
            $salesQuery->whereHas('seller', function ($query) use ($sellerType) {
                $query->where('type', $sellerType);
            });
        }

        $sales = $salesQuery->get();

        $this->data = [];
        $this->totalsByInterval = [];
        $this->grandTotal = 0;

        foreach ($sales as $sale) {
            $sellerName = $sale->seller->name; // Obter o nome do vendedor
            $key = "{$sale->initial_date} a {$sale->final_date}";
            $this->data[$sellerName][$key] = $sale->value; // Incrementar a soma total do vendedor

            if (!isset($this->data[$sellerName]['total'])) {
                $this->data[$sellerName]['total'] = 0;
            }
            $this->data[$sellerName]['total'] += $sale->value;
            // Calcular o total por intervalo
            if (!isset($this->totalsByInterval[$key])) {
                $this->totalsByInterval[$key] = 0;
            }
            $this->totalsByInterval[$key] += $sale->value;

            // Calcular o total geral
            $this->grandTotal += $sale->value;
        }

        // Garantir que todas as colunas estejam presentes para cada vendedor
        foreach ($this->data as $sellerName => $intervals) {
            foreach ($this->dateIntervals as $interval) {
                $key = "{$interval->initial_date} a {$interval->final_date}";
                if (!isset($this->data[$sellerName][$key])) {
                    $this->data[$sellerName][$key] = 0; // Valor padrão
                }
            }
        }

        return response()->json([
            'data' => $this->data,
            'totalsByInterval' => $this->totalsByInterval,
            'grandTotal' => $this->grandTotal,
        ]);
    }

    public function clearFilters()
    {
        // Define as datas para o início e fim do mês atual
        $initialDate = Carbon::now()->startOfMonth()->toDateString();
        $finalDate = Carbon::now()->endOfMonth()->toDateString();

        // Filtrar os intervalos de datas do mês atual
        $this->dateIntervals = Invoicing::select('initial_date', 'final_date')
            ->whereBetween('final_date', [$initialDate, $finalDate])
            ->distinct()
            ->orderBy('final_date')
            ->get();

        // Filtrar as vendas com base nos intervalos de datas obtidos
        $sales = Invoicing::with('seller')
            ->select('seller_id', 'initial_date', 'final_date', 'value')
            ->whereIn('initial_date', $this->dateIntervals->pluck('initial_date'))
            ->get();

        $this->data = [];
        $this->totalsByInterval = [];
        $this->grandTotal = 0;

        foreach ($sales as $sale) {
            $sellerName = $sale->seller->name; // Obter o nome do vendedor
            $key = "{$sale->initial_date} a {$sale->final_date}";
            $this->data[$sellerName][$key] = $sale->value;

            // Incrementar a soma total do vendedor
            if (!isset($this->data[$sellerName]['total'])) {
                $this->data[$sellerName]['total'] = 0;
            }
            $this->data[$sellerName]['total'] += $sale->value;
            // Calcular o total por intervalo
            if (!isset($this->totalsByInterval[$key])) {
                $this->totalsByInterval[$key] = 0;
            }
            $this->totalsByInterval[$key] += $sale->value; // Calcular o total geral
            $this->grandTotal += $sale->value;
        }

        // Garantir que todas as colunas estejam presentes para cada vendedor
        foreach ($this->data as $sellerName => $intervals) {
            foreach ($this->dateIntervals as $interval) {
                $key = "{$interval->initial_date} a {$interval->final_date}";
                if (!isset($this->data[$sellerName][$key])) {
                    $this->data[$sellerName][$key] = 0; // Valor padrão
                }
            }
        }

        return response()->json(['data' => $this->data, 'totalsByInterval' => $this->totalsByInterval, 'grandTotal' => $this->grandTotal,]);
    }
        */



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
            return session()->flash('error', 'Por favor, selecione um mês para filtrar.');
        }

        $this->currentMonth = $this->filterMonth;
        $this->previousMonth = $this->filterMonth;

        $this->defaultFilter();

        $this->dispatch('update-chart', ['data' => $this->data]);
    }

    public function render()
    {
        return view('livewire.details-invoicing');
    }
}
