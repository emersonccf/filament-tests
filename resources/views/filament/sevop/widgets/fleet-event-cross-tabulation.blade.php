<x-filament-widgets::widget>
    <x-filament::card>
        {{-- Cabeçalho do Widget --}}
        <div class="flex items-center justify-between gap-x-3">
            <h3 class="text-lg font-semibold leading-6 text-gray-950 dark:text-white">
                {{ static::$heading }} {{-- Acessa a propriedade estática diretamente --}}
            </h3>
        </div>

        {{-- Total Geral de Ocorrências --}}
        <div class="mt-4 mb-4 text-center text-xl font-bold text-primary-600 dark:text-primary-400">
            Total de Ocorrências: {{ $this->grandTotal }}
        </div>

        {{-- Tabela Responsiva --}}
        <div class="overflow-x-auto">
            @if ($this->grandTotal > 0)
                <table class="w-full text-left text-sm text-gray-500 dark:text-gray-400">
                    {{-- Cabeçalho da Tabela --}}
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3 rounded-tl-lg">Tipo de Evento</th>
                        @foreach ($this->eventStatuses as $statusValue => $statusLabel)
                            <th scope="col" class="px-6 py-3">{{ $statusLabel }}</th>
                        @endforeach
                        <th scope="col" class="px-6 py-3 rounded-tr-lg">Total por Tipo</th>
                    </tr>
                    </thead>
                    {{-- Corpo da Tabela --}}
                    <tbody>
                    @foreach ($this->eventTypes as $typeValue => $typeLabel)
                        <tr class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $typeLabel }}
                            </th>
                            @foreach ($this->eventStatuses as $statusValue => $statusLabel)
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $this->pivotTable[$typeValue][$statusValue] ?? 0 }}
                                </td>
                            @endforeach
                            <td class="px-6 py-4 font-bold text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $this->rowTotals[$typeValue] ?? 0 }}
                            </td>
                        </tr>
                    @endforeach
                    {{-- Linha de Totalização de Colunas --}}
                    <tr class="bg-gray-50 dark:bg-gray-700 font-bold text-gray-700 dark:text-gray-400 uppercase text-xs">
                        <th scope="row" class="px-6 py-3 rounded-bl-lg">Total por Status</th>
                        @foreach ($this->eventStatuses as $statusValue => $statusLabel)
                            <td class="px-6 py-3 whitespace-nowrap">
                                {{ $this->columnTotals[$statusValue] ?? 0 }}
                            </td>
                        @endforeach
                        <td class="px-6 py-3 rounded-br-lg bg-primary-500 text-white whitespace-nowrap">
                            {{ $this->grandTotal }}
                        </td>
                    </tr>
                    </tbody>
                </table>
            @else
                <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                    Nenhuma ocorrência de evento encontrada para veículos ativos.
                </div>
            @endif
        </div>
    </x-filament::card>
</x-filament-widgets::widget>
