<?php

namespace App\Filament\Sevop\Resources\BdvMainResource\Pages;

use App\Enums\TipoRegistroStatusEnum;
use App\Filament\Sevop\Resources\BdvMainResource;
use App\Models\BdvMain; // Necessário para type-hinting do record
use App\Models\BdvItemStatus; // Necessário para campos booleanos
use App\Models\BdvRegistroMotorista; // Necessário para buscar o registro motorista
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Carbon\Carbon; // Para formatação de data

class ViewBdvMain extends ViewRecord
{
    protected static string $resource = BdvMainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
//        // Esta lógica é idêntica à de EditBdvMain, pois ambos precisam carregar os dados.
//        $record = $this->getRecord();

        // Garanta que o record principal (BdvMain) carregue as relações necessárias
        $record = $this->getRecord()->loadMissing('veiculo.modelo'); // <<< ADICIONE/AJUSTE ESTA LINHA

        $data['numero_rodas_veiculo'] = $record->veiculo->modelo->numero_rodas ?? 0;

        $firstRegistroMotorista = $record->registrosMotorista()->first();

        if ($firstRegistroMotorista) {
            $data['bdv_registro_motorista'] = $firstRegistroMotorista->toArray();

            if ($firstRegistroMotorista->momento_saida instanceof Carbon) {
                $data['bdv_registro_motorista']['momento_saida'] = $firstRegistroMotorista->momento_saida->format('Y-m-d H:i:s');
            }

            $saidaStatus = $firstRegistroMotorista->itemStatus()
                ->where('tipo_registro', TipoRegistroStatusEnum::SAIDA)
                ->first();

            if ($saidaStatus) {
                foreach (BdvItemStatus::BOOLEAN_FIELDS as $field) {
                    $data['bdv_item_status_saida'][$field] = $saidaStatus->$field;
                }
            }

            $chegadaStatus = $firstRegistroMotorista->itemStatus()
                ->where('tipo_registro', TipoRegistroStatusEnum::CHEGADA)
                ->first();
            if ($chegadaStatus) {
                foreach (BdvItemStatus::BOOLEAN_FIELDS as $field) {
                    $data['bdv_item_status_chegada'][$field] = $chegadaStatus->$field;
                }
            }
        }

        return $data;
    }
}
