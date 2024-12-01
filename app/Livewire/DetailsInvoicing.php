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

    public $grandTotals = ['nfe' => 0, 'boleto' => 0];

    public $chartData = [];

    public $months = [];

    public $currentMonth = '';

    public $previousMonth = '';

    public $filterMonth = '';

    public $currentYear = '';

    public $previousYear = '';

    public $filterInitialDate = '';

    public $filterRole = '';

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

        $this->filterRole = 'all';

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

        $this->filterRole = 'all';

        $this->defaultFilter();
    }

    public function filterSales($currentMonth, $currentYear, $previousMonth = null, $previousYear = null)
    {
        $previousMonth = $previousMonth ?? $currentMonth;
        $previousYear = $previousYear ?? $currentYear;

        // Buscar intervalos de datas
        $dateIntervals = Invoicing::select('initial_date', 'final_date', 'month_sale')
            ->where(function ($query) use ($currentMonth, $currentYear) {
                $query->whereMonth('final_date', $currentMonth)
                    ->whereYear('final_date', $currentYear)
                    ->orWhereMonth('initial_date', $currentMonth)
                    ->whereYear('initial_date', $currentYear);
            })
            ->where('month_sale', $currentMonth)
            ->distinct()
            ->orderBy('final_date')
            ->get();

        if ($dateIntervals->isEmpty()) {
            $dateIntervals = Invoicing::select('initial_date', 'final_date', 'month_sale')
                ->where(function ($query) use ($previousMonth, $previousYear) {
                    $query->whereMonth('final_date', $previousMonth)
                        ->whereYear('final_date', $previousYear)
                        ->orWhereMonth('initial_date', $previousMonth)
                        ->whereYear('initial_date', $previousYear);
                })
                ->where('month_sale', $previousMonth)
                ->distinct()
                ->orderBy('final_date')
                ->get();
        }

        //dd($dateIntervals, $currentMonth);

        // Buscar vendas
        $salesQuery = Invoicing::with(['seller' => function ($query) {
            $query->select('id', 'name', 'role');
        }])
            ->select('seller_id', 'initial_date', 'final_date', 'nfe_value', 'bol_value')
            ->whereIn('initial_date', $dateIntervals->pluck('initial_date'));

        if ($this->filterRole !== 'all') {
            $salesQuery->whereHas('seller', function ($query) {
                $query->where('role', $this->filterRole);
            });
        }

        $sales = $salesQuery->get();

        // Organizar os dados
        $data = [];
        $totalsByInterval = [];
        $grandTotals = ['nfe' => 0, 'boleto' => 0];

        foreach ($sales as $sale) {
            $sellerName = $sale->seller->name;
            $key = "{$sale->initial_date} a {$sale->final_date}";

            if (!isset($data[$sellerName])) {
                $data[$sellerName] = ['nfe_total' => 0, 'bol_total' => 0];
            }

            $data[$sellerName][$key]['nfe'] = $sale->nfe_value ?? 0;
            $data[$sellerName][$key]['boleto'] = $sale->bol_value ?? 0;

            $data[$sellerName]['nfe_total'] += $sale->nfe_value;
            $data[$sellerName]['bol_total'] += $sale->bol_value;

            if (!isset($totalsByInterval[$key])) {
                $totalsByInterval[$key] = ['nfe' => 0, 'boleto' => 0];
            }

            $totalsByInterval[$key]['nfe'] += $sale->nfe_value;
            $totalsByInterval[$key]['boleto'] += $sale->bol_value;

            $grandTotals['nfe'] += $sale->nfe_value;
            $grandTotals['boleto'] += $sale->bol_value;
        }

        return [
            'data' => $data,
            'totalsByInterval' => $totalsByInterval,
            'grandTotals' => $grandTotals,
            'dateIntervals' => $dateIntervals,
        ];
    }

    public function applyDateRangeFilter()
    {
        if (!$this->filterInitialDate || !$this->filterFinalDate) {
            return session()->flash('error', 'Por favor, selecione ambas as datas para aplicar o filtro.');
        }

        $this->filterMonth = ''; // Limpar o filtro de mês, caso seja necessário.

        // Validar que a data inicial não seja maior que a final
        if (Carbon::parse($this->filterInitialDate)->gt(Carbon::parse($this->filterFinalDate))) {
            return session()->flash('error', 'A data inicial não pode ser maior que a data final.');
        }

        // Construção da consulta
        $salesQuery = Invoicing::with(['seller' => function ($query) {
            $query->select('id', 'name', 'role'); // Certifique-se de carregar a role dos vendedores
        }])
            ->select('seller_id', 'initial_date', 'final_date', 'nfe_value', 'bol_value')
            ->where(function ($query) {
                // Considera as vendas cujas datas (início ou fim) estejam no intervalo selecionado
                $query->whereBetween('final_date', [$this->filterInitialDate, $this->filterFinalDate])
                    ->orWhereBetween('initial_date', [$this->filterInitialDate, $this->filterFinalDate])
                    ->orWhere(function ($query) {
                        // Inclui vendas que começam antes e terminam depois do intervalo
                        $query->where('initial_date', '<', $this->filterInitialDate)
                            ->where('final_date', '>', $this->filterFinalDate);
                    });
            });

        // Aplicar o filtro de role
        if ($this->filterRole !== 'all') {
            $salesQuery->whereHas('seller', function ($query) {
                $query->where('role', $this->filterRole);
            });
        }

        // Obter os dados filtrados
        $sales = $salesQuery->get();

        // Processar as vendas
        $this->processSales($sales);
    }

    protected function processSales($sales)
    {
        $this->data = [];
        $this->totalsByInterval = [];
        $this->grandTotals = ['nfe' => 0, 'boleto' => 0];  // Inicialização corrigida

        //dd($sales);

        foreach ($sales as $sale) {
            // Verifique se o item é um array ou objeto
            $sellerName = $sale->seller->name; // Acessando seller_id (ou use o nome conforme necessário)
            $key = "{$sale['initial_date']} a {$sale['final_date']}"; // Acesso por índice

            // Atribuindo o valor para nfe_value ou bol_value com verificação de null
            $nfe_value = $sale['nfe_value'] ?? 0;
            $bol_value = $sale['bol_value'] ?? 0;

            // Atualizando os dados da venda
            $this->data[$sellerName][$key] = $nfe_value + $bol_value;  // Somando os dois valores

            // Calculando o total por vendedor
            if (!isset($this->data[$sellerName]['nfe_total']) || !isset($this->data[$sellerName]['bol_total'])) {
                $this->data[$sellerName]['nfe_total'] = 0;
                $this->data[$sellerName]['bol_total'] = 0;
            }

            $this->data[$sellerName]['nfe_total'] += $nfe_value;
            $this->data[$sellerName]['bol_total'] += $bol_value;

            // Calculando os totais por intervalo
            if (!isset($this->totalsByInterval[$key])) {
                $this->totalsByInterval[$key] = 0;
            }
            $this->totalsByInterval[$key] += $nfe_value + $bol_value;

            // Somando os totais gerais de NFE e Boleto
            $this->grandTotals['nfe'] += $nfe_value;
            $this->grandTotals['boleto'] += $bol_value;
        }

        //dd($this->data);

        // Atualizar o gráfico com os dados filtrados
        $this->dispatch('update-chart', ['data' => $this->data]);
    }

    public function defaultFilter()
    {
        // Aplicar os filtros de vendas usando os parâmetros de data atuais e anteriores
        $filters = $this->filterSales(
            $this->currentMonth,
            $this->currentYear,
            $this->previousMonth,
            $this->previousYear
        );

        // Atualizar as propriedades do componente com os dados filtrados
        $this->data = $filters['data'];
        $this->totalsByInterval = $filters['totalsByInterval'];
        $this->grandTotals = $filters['grandTotals']; // Inclui totais separados para NFE e Boleto
        $this->dateIntervals = $filters['dateIntervals'];

        //dd($this->data, array_column($this->data, 'nfe_total'));

        // Preparar os dados do gráfico. Aqui, você pode adaptar para exibir valores separados de NFE e Boleto.

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
        //$this->dispatch('update-chart', ['data' => $this->data]);
    }

    public function render()
    {
        return view('livewire.details-invoicing');
    }
}
