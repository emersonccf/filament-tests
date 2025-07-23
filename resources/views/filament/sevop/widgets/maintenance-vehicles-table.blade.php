<x-filament-widgets::widget>
    <x-filament::card>
        {{-- Cabeçalho do Widget --}}
        <div class="flex items-center justify-between gap-x-3">
            <h3 class="text-lg font-semibold leading-6 text-gray-950 dark:text-white">
                {{ static::$heading }} {{-- Acessa a propriedade estática diretamente --}}
            </h3>
        </div>

        {{-- Total de Veículos em Manutenção --}}
        <div class="mt-4 mb-4 text-center text-xl font-bold text-primary-600 dark:text-primary-400">
            Total de Veículos em Manutenção: {{ $this->totalMaintenanceVehicles }}
        </div>

        {{-- Tabela Responsiva --}}
        <div class="overflow-x-auto">
            @if ($this->totalMaintenanceVehicles > 0)
                <table class="w-full text-left text-sm text-gray-500 dark:text-gray-400">
                    {{-- Cabeçalho da Tabela --}}
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">Placa</th>
                        <th scope="col" class="px-6 py-3">Marca / Modelo</th>
                        <th scope="col" class="px-6 py-3">Prefixo</th>
                        <th scope="col" class="px-6 py-3">Local de Ativação</th>
                    </tr>
                    </thead>
                    {{-- Corpo da Tabela --}}
                    <tbody>
                    @foreach ($this->maintenanceVehicles as $vehicle)
                        <tr class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{-- ADICIONADO: Link para a página de edição do veículo --}}
                                <a
                                    href="{{ \App\Filament\Sevop\Resources\VeiculoResource::getUrl('edit', ['record' => $vehicle['id_veiculo']]) }}"
                                    class="text-primary-600 hover:underline dark:text-primary-400"
                                >
                                    {{ $vehicle['placa'] }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $vehicle['marca_modelo'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $vehicle['prefixo_veiculo'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $vehicle['local_ativacao'] }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                    Nenhum veículo encontrado em manutenção.
                </div>
            @endif
        </div>
    </x-filament::card>
</x-filament-widgets::widget>
