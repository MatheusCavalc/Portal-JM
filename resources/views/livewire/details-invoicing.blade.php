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
                            class="absolute left-0 z-50 px-4 py-2 mt-2 bg-white dark:bg-gray-950 dark:text-white rounded-md shadow-md w-72">

                            <div class="flex justify-between my-3">
                                <p class="font-bold">Filtros</p>

                                <button wire:click='clearFilters'
                                    class="text-sm font-bold text-red-500 hover:underline">Limpar Filtros</button>
                            </div>

                            <div>
                                <label for="countries"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Selecione um
                                    Mes</label>
                                <select id="countries" wire:model="filterMonth"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    <option selected disabled>Escolha um mes</option>
                                    @foreach ($months as $key => $month)
                                        <option value="{{ $key }}">{{ $month }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex justify-end my-4">
                                <button wire:click='addFilters'
                                    class="text-sm font-bold text-blue-500 hover:underline">Aplicar Filtros</button>
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
                        Dados do mês de
                        @if ($this->currentMonth)
                            {{ $months[$this->currentMonth] }}
                        @elseif ($this->previousMonth)
                            {{ $months[$this->previousMonth] }}
                        @elseif ($this->filterMonth)
                            {{ $months[$this->filterMonth] }}
                        @endif
                        de {{ $this->currentYear }}
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
                            <tr
                                class="bg-white border-b hover:bg-gray-50">
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
                        <tr
                            class="font-bold bg-white border-b hover:bg-gray-50">
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
