<div>
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
                            class="flex items-center gap-2 focus:outline-none text-white bg-yellow-600 hover:bg-yellow-500 font-medium rounded-md text-sm px-3 py-2 me-2 mb-2">
                            Adicionar Filtro

                            <!-- Heroicon: chevron-down -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>

                        <!-- Panel -->
                        <div x-ref="panel" x-show="open" x-transition.origin.top.left
                            x-on:click.outside="close($refs.button)" :id="$id('dropdown-button')" style="display: none;"
                            class="absolute -left-20 z-50 px-4 py-2 mt-2 bg-white dark:bg-gray-950 dark:text-white rounded-md shadow-md w-96">

                            <div class="flex justify-between my-3">
                                <p class="font-bold">Filtros</p>

                                <button wire:click='defaultFilter'
                                    class="text-sm font-bold text-red-500 hover:underline">Limpar Filtros</button>
                            </div>

                            <!--Month Filter-->
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
                                @if (session()->has('error'))
                                    <div class="text-red-500 text-sm my-2">
                                        {{ session('error') }}
                                    </div>
                                @endif

                                <div class="flex justify-end my-4">
                                    <button type="submit"
                                        class="px-4 py-2 bg-blue-500 text-white text-sm font-bold rounded hover:bg-blue-600">
                                        Aplicar Filtro Mensal
                                    </button>
                                </div>
                            </form>

                            <div>
                                <label for="date-range-picker" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Selecione um Intervalo de Datas
                                </label>
                                <div class="flex items-center space-x-2">
                                    <div class="relative w-36">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                                            </svg>
                                        </div>
                                        <input id="datepicker-range-start" wire:model="filterInitialDate" type="date"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="Data inicial">
                                    </div>
                                    <span class="text-gray-500">até</span>
                                    <div class="relative w-36">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                                            </svg>
                                        </div>
                                        <input id="datepicker-range-end" wire:model="filterFinalDate" type="date"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="Data final">
                                    </div>
                                </div>
                                <div class="flex justify-end mt-4">
                                    <button type="button" wire:click="applyDateRangeFilter"
                                        class="px-4 py-2 bg-blue-500 text-white text-sm font-bold rounded hover:bg-blue-600">
                                        Aplicar Filtro
                                    </button>
                                </div>
                            </div>

                            <div class="mt-2">
                                @if (session()->has('error'))
                                    <div class="text-red-500 text-sm">
                                        {{ session('error') }}
                                    </div>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>

                <button type="button" onclick="printTable()"
                    class="focus:outline-none text-white bg-yellow-600 hover:bg-yellow-500 font-medium rounded-md text-sm px-3 py-2 me-2 mb-2">
                    Imprimir Dados
                </button>
            </div>
        </div>

        <div class="content">
            <div class="mb-5">
                <div>
                    <p class="text-xl font-bold">
                        Dados do
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

            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left text-gray-500 rtl:text-right">
                    <thead class="text-xs text-gray-700 bg-gray-50">
                        <tr>
                            <th scope="col" class="px-3 py-3">
                                Vendedor
                            </th>
                            @foreach ($dateIntervals as $interval)
                                <th scope="col" class="px-3 py-3">
                                    {{ date('d/m', strtotime($interval->initial_date)) }} a
                                    {{ date('d/m', strtotime($interval->final_date)) }}
                                </th>
                            @endforeach
                            <th scope="col" class="px-3 py-3">
                                Total Mes
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $sellerName => $values)
                            <tr class="bg-white border-b hover:bg-gray-50">
                                <td class="px-3 py-4">{{ $sellerName }}</td>
                                @foreach ($dateIntervals as $interval)
                                    @php
                                        $key = "{$interval->initial_date} a {$interval->final_date}";
                                    @endphp
                                    <td class="px-3 py-4">R$ {{ number_format($values[$key], 2, ',', '.') }}</td>
                                @endforeach
                                <td class="px-3 py-4">R$ {{ number_format($values['total'], 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        <tr class="font-bold bg-white border-b hover:bg-gray-50">
                            <td class="px-3 py-4">Total Geral</td>
                            @foreach ($dateIntervals as $interval)
                                @php $key = "{$interval->initial_date} a {$interval->final_date}" @endphp
                                <td class="px-3 py-4">
                                    R$ {{ number_format($totalsByInterval[$key] ?? 0, 2, ',', '.') }}</td>
                            @endforeach
                            <td class="px-3 py-4">
                                R$ {{ number_format($grandTotal ?? 0, 2, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="flex justify-center mt-5 dark:bg-white">
        <div id="chart"></div>
    </div>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        function printTable() {
            const item = document.querySelector(".content");

            var opt = {
                margin: 0.5,
                filename: "file.pdf",
                html2canvas: {
                    scale: 2
                },
                jsPDF: {
                    unit: "in",
                    format: "letter",
                    orientation: "portrait"
                }
            };

            html2pdf().set(opt).from(item).save();
        }
    </script>

    <script>
        function loadChart(chartData) {
            const series = Object.values(chartData).map(seller => seller.total); // Valores totais
            const labels = Object.keys(chartData); // Nomes dos vendedores

            const options = {
                series: series,
                chart: {
                    type: 'pie',
                    width: 380,
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

            var chart = new ApexCharts(document.querySelector("#chart"), options);
            chart.render();
        }
    </script>

    @script
        <script>
            $wire.on('update-chart', (data) => {
                loadChart(data[0].data)
            });
        </script>
    @endscript
</div>
