<x-filament-widgets::widget>
    <x-filament::card>
        {{-- Cabeçalho do Widget --}}
        <div class="flex items-center justify-between gap-x-3">
            <h3 class="text-lg font-semibold leading-6 text-gray-950 dark:text-white">
                {{ static::$heading }}  {{-- <-- CORREÇÃO AQUI --}}
            </h3>
        </div>

        {{-- Tabela Responsiva --}}
        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-500 dark:text-gray-400">
                {{-- Cabeçalho da Tabela --}}
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">Local de Ativação</th>
                    <th scope="col" class="px-6 py-3 text-center">2 Rodas</th>
                    <th scope="col" class="px-6 py-3 text-center">4 Rodas</th>
                    <th scope="col" class="px-6 py-3 text-center">Total</th>
                </tr>
                </thead>
                {{-- Corpo da Tabela --}}
                <tbody>
                @foreach ($this->tableData as $row)
                    <tr class="
                            @if(isset($row['is_total_row']) && $row['is_total_row'])
                                bg-gray-100 dark:bg-gray-800 font-bold text-base
                            @else
                                bg-white border-b dark:bg-gray-900 dark:border-gray-700
                            @endif
                        ">
                        <td class="px-6 py-4 whitespace-nowrap">{{ $row['location_label'] }}</td>
                        <td class="px-6 py-4 text-center">{{ $row['two_wheelers'] }}</td>
                        <td class="px-6 py-4 text-center">{{ $row['four_wheelers'] }}</td>
                        <td class="px-6 py-4 text-center">{{ $row['total'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </x-filament::card>
</x-filament-widgets::widget>
