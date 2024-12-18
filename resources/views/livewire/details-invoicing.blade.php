<div class="dark:bg-gray-950">
    <div>
        <div class="flex items-center justify-end mb-5">
            <div class="flex gap-1">
                <div class="flex justify-center">
                    <div x-data="{
                        open: false,
                        toggle() {
                            if (this.open) {
                                return this.close()
                            }

                            this.$refs.button.focus()

                            this.open = true
                        },
                        close(focusAfter) {
                            if (!this.open) return

                            this.open = false

                            focusAfter && focusAfter.focus()
                        }
                    }" x-on:keydown.escape.prevent.stop="close($refs.button)"
                        x-on:focusin.window="! $refs.panel.contains($event.target) && close()" x-id="['dropdown-button']"
                        class="relative">
                        <!-- Button -->
                        <button x-ref="button" x-on:click="toggle()" :aria-expanded="open"
                            :aria-controls="$id('dropdown-button')" type="button"
                            class="flex items-center gap-2 focus:outline-none text-white bg-[#d97706] hover:bg-yellow-500 font-medium rounded-md text-sm px-3 py-2 me-2 mb-2">
                            Adicionar Filtro

                            <!-- Heroicon: chevron-down -->
                            <svg xmlns="http://www.w3.org/2000/svg" :class="{ '-rotate-180': open }"
                                class="w-5 h-5 text-white transition duration-500 ease-in-out" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>

                        <!-- Panel -->
                        <div x-ref="panel" x-show="open" x-transition.origin.top.left
                            x-on:click.outside="close($refs.button)" :id="$id('dropdown-button')" style="display: none;"
                            class="absolute -5 lg:-left-20 z-50 px-4 py-5 mt-2 bg-white dark:bg-gray-950 dark:text-white rounded-md shadow-lg w-72 lg:w-96">

                            <div class="flex justify-between mb-3">
                                <p class="font-bold">Filtros</p>

                                <button wire:click='cleanFilters'
                                    class="text-sm font-bold text-red-500 hover:underline">Limpar Filtros</button>
                            </div>

                            <!-- Role Filter -->
                            <div class="mb-4">
                                <label for="filterRole"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Selecione a Categoria de Venda
                                </label>
                                <select id="filterRole" wire:model="filterRole"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    <option value="all" selected>Todas</option>
                                    <option value="Pré Venda">Pré Venda</option>
                                    <option value="Pronta Entrega">Pronta Entrega</option>
                                </select>
                            </div>

                            <hr class="h-px my-3 bg-gray-200 border-0 dark:bg-gray-700">

                            <!-- Month Filter -->
                            <form wire:submit.prevent="applyFilters">
                                <label for="filterMonth"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Selecione um Mês
                                </label>
                                <select id="filterMonth" wire:model="filterMonth"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    <option selected>Escolha um mês</option>
                                    @foreach ($months as $key => $month)
                                        <option value="{{ $key }}">{{ $month }}</option>
                                    @endforeach
                                </select>
                                @if (session()->has('errorMonth'))
                                    <div class="text-red-500 text-sm my-2">
                                        {{ session('errorMonth') }}
                                    </div>
                                @endif

                                <div class="flex justify-end my-4">
                                    <button type="submit"
                                        class="px-4 py-2 bg-blue-500 text-white text-sm font-bold rounded-md hover:bg-blue-600">
                                        Aplicar Filtro Mensal
                                    </button>
                                </div>
                            </form>

                            <!-- Month Range Filter -->
                            <form wire:submit.prevent="applyDateRangeFilter">
                                <label for="date-range-picker"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Selecione um Intervalo de Datas
                                </label>
                                <div class="flex flex-col lg:flex-row justify-center items-center gap-2 lg:gap-3">
                                    <div class="relative w-full lg:w-40">
                                        <div
                                            class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400"
                                                xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path
                                                    d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                                            </svg>
                                        </div>
                                        <input id="datepicker-range-start" wire:model="filterInitialDate" type="date"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-md-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="Data inicial">
                                    </div>
                                    <span class="text-gray-500">a</span>
                                    <div class="relative w-full lg:w-40">
                                        <div
                                            class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400"
                                                xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path
                                                    d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                                            </svg>
                                        </div>
                                        <input id="datepicker-range-end" wire:model="filterFinalDate" type="date"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="Data final">
                                    </div>
                                </div>

                                <div class="mt-2">
                                    @if (session()->has('error'))
                                        <div class="text-red-500 text-sm">
                                            {{ session('error') }}
                                        </div>
                                    @endif
                                </div>

                                <div class="flex justify-end mt-4">
                                    <button type="submit"
                                        class="px-4 py-2 bg-blue-500 text-white text-sm font-bold rounded hover:bg-blue-600">
                                        Aplicar Filtro de Intervalo de Datas
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>

                <button type="button" onclick="printTable()"
                    class="text-white bg-[#d97706] hover:bg-yellow-500 font-medium rounded-md text-sm px-3 py-2 me-2 mb-2">
                    Imprimir Dados
                </button>
            </div>
        </div>

        <div class="content">
            <div class="mb-5">
                <div>
                    <p class="text-xl font-bold">
                        Dados
                        @if ($this->filterRole == 'Pré Venda')
                            da Pré Venda
                        @elseif ($this->filterRole == 'Pronta Entrega')
                            da Pronta Entrega
                        @else
                        @endif
                        do
                        @if ($this->filterInitialDate && $this->filterFinalDate)
                            intervalo de {{ \Carbon\Carbon::parse($this->filterInitialDate)->format('d/m/Y') }}
                            a {{ \Carbon\Carbon::parse($this->filterFinalDate)->format('d/m/Y') }}
                        @elseif ($this->filterMonth)
                            mês de {{ $months[$this->filterMonth] }} de {{ $this->currentYear }}
                        @elseif ($this->currentMonth)
                            mês de {{ $months[$this->currentMonth] }} de {{ $this->currentYear }}
                        @elseif ($this->previousMonth)
                            mês de {{ $months[$this->previousMonth] }} de {{ $this->previousYear }}
                        @else
                            período selecionado
                        @endif
                    </p>
                </div>
            </div>

            <div class="relative overflow-x-auto shadow-lg sm:rounded-lg border border-gray-300">
                <table class="w-full text-sm text-left text-gray-500 rtl:text-right">
                    <thead class="text-xs text-gray-700 bg-gray-50">
                        <tr>
                            <th scope="col" class="px-2 py-3">Vendedor</th>
                            @foreach ($dateIntervals as $interval)
                                <th colspan="2" class="px-2 py-3 text-center border-r-2 border-gray-300">
                                    {{ date('d/m', strtotime($interval->initial_date)) }} a
                                    {{ date('d/m', strtotime($interval->final_date)) }}
                                </th>
                            @endforeach
                            <th colspan="2" class="px-2 py-3 text-center">Total Geral</th>
                        </tr>
                        <tr>
                            <th></th>
                            @foreach ($dateIntervals as $interval)
                                <th class="px-2 py-3 text-center">NFE</th>
                                <th class="px-2 py-3 text-center border-r-2 border-gray-300">Boleto</th>
                            @endforeach
                            <th class="px-2 py-3 text-center">NFE</th>
                            <th class="px-2 py-3 text-center">Boleto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $sellerName => $values)
                            <tr class="bg-white border-b hover:bg-gray-50">
                                <td class="px-2 py-4">{{ $sellerName }}</td>
                                @foreach ($dateIntervals as $interval)
                                    @php
                                        $key = "{$interval->initial_date} a {$interval->final_date}";
                                    @endphp
                                    <td class="px-2 py-4 text-center">
                                        R$ {{ number_format($values[$key]['nfe'] ?? 0, 2, ',', '.') }}
                                    </td>
                                    <td class="px-2 py-4 text-center border-r-2 border-gray-300">
                                        R$ {{ number_format($values[$key]['boleto'] ?? 0, 2, ',', '.') }}
                                    </td>
                                @endforeach
                                <td class="px-2 py-4 text-center">
                                    R$ {{ number_format($values['nfe_total'] ?? 0, 2, ',', '.') }}
                                </td>
                                <td class="px-2 py-4 text-center">
                                    R$ {{ number_format($values['bol_total'] ?? 0, 2, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                        <tr class="font-bold bg-gray-50">
                            <td class="px-2 py-4">Total Geral</td>
                            @foreach ($dateIntervals as $interval)
                                @php
                                    $key = "{$interval->initial_date} a {$interval->final_date}";
                                @endphp
                                <td class="px-2 py-4 text-center">
                                    R$ {{ number_format($totalsByInterval[$key]['nfe'] ?? 0, 2, ',', '.') }}
                                </td>
                                <td class="px-2 py-4 text-center border-r-2 border-gray-300">
                                    R$ {{ number_format($totalsByInterval[$key]['boleto'] ?? 0, 2, ',', '.') }}
                                </td>
                            @endforeach
                            <td class="px-2 py-4 text-center">
                                R$ {{ number_format($grandTotals['nfe'], 2, ',', '.') }}
                            </td>
                            <td class="px-2 py-4 text-center">
                                R$ {{ number_format($grandTotals['boleto'], 2, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row justify-center mt-5 gap-5">
        <div>
            <p class="text-2xl font-bold text-left mb-4">Faturamento em NFE's</p>

            <div id="chart-nfe"></div>
        </div>

        <div>
            <p class="text-2xl font-bold text-left mb-4">Faturamento em Boletos</p>

            <div id="chart-bol"></div>
        </div>
    </div>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        function printTable() {
            const item = document.querySelector(".content");

            var opt = {
                margin: 0,
                filename: "file.pdf",
                html2canvas: {
                    scale: 2
                },
                jsPDF: {
                    unit: "in",
                    format: "a4", // Certifique-se de usar o formato A4
                    orientation: "landscape" // Define a orientação como paisagem
                }
            };

            html2pdf().set(opt).from(item).save();
        }
    </script>

    <script>
        function loadChartNFE(nfeData) {
            const series = Object.values(nfeData); // Totais de NFE
            const labels = Object.keys(nfeData); // Nomes dos vendedores

            const options = {
                series: series,
                chart: {
                    type: 'pie',
                    width: 550,
                },
                labels: labels,
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200,
                        },
                        legend: {
                            position: 'bottom',
                        },
                    },
                }],
            };

            // Renderizar o gráfico no elemento com ID `chart-nfe`
            const chart = new ApexCharts(document.querySelector("#chart-nfe"), options);
            chart.render();
        }

        function loadChartBoleto(bolData) {
            const series = Object.values(bolData); // Totais de NFE
            const labels = Object.keys(bolData); // Nomes dos vendedores

            const options = {
                series: series,
                chart: {
                    type: 'pie',
                    width: 550,
                },
                labels: labels,
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200,
                        },
                        legend: {
                            position: 'bottom',
                        },
                    },
                }],
            };

            // Renderizar o gráfico no elemento com ID `chart-nfe`
            const chart = new ApexCharts(document.querySelector("#chart-bol"), options);
            chart.render();
        }
    </script>

    @script
        <script>
            $wire.on('update-chart', (data) => {

                console.log('Oi')

                // Acessar o objeto de dados
                const chartData = data['0']['data'];

                // Limpar gráficos existentes
                document.querySelector("#chart-nfe").innerHTML = '';
                document.querySelector("#chart-bol").innerHTML = '';

                // Separar os dados de NFE e Boleto
                const nfeData = {};
                const bolData = {};

                Object.keys(chartData).forEach((sellerName) => {
                    if (
                        chartData[sellerName]?.nfe_total !== undefined &&
                        chartData[sellerName]?.bol_total !== undefined
                    ) {
                        nfeData[sellerName] = chartData[sellerName].nfe_total;
                        bolData[sellerName] = chartData[sellerName].bol_total;
                    }
                });

                // Renderizar os gráficos
                loadChartNFE(nfeData);
                loadChartBoleto(bolData);
            });
        </script>
    @endscript
</div>
