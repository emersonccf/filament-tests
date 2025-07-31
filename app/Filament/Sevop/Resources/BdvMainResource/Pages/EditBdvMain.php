<?php

namespace App\Filament\Sevop\Resources\BdvMainResource\Pages;

use App\Enums\TipoRegistroStatusEnum;
use App\Filament\Sevop\Resources\BdvMainResource;
use App\Models\BdvMain; // Necessário para type-hinting do record
use App\Models\BdvItemStatus; // Necessário para campos booleanos
use App\Models\BdvRegistroMotorista; // Necessário para buscar o registro motorista
use Filament\Resources\Pages\EditRecord;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException; // ADICIONE ESTE IMPORT NO TOPO
use Filament\Notifications\Notification; // ADICIONE ESTE IMPORT NO TOPO
use Illuminate\Support\Str; // ADICIONE ESTE IMPORT NO TOPO, se ainda não tiver


// Para formatação de data

class EditBdvMain extends EditRecord
{
    protected static string $resource = BdvMainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Se você tiver ações como DeleteAction, ViewAction, etc., coloque-as aqui
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Garanta que o record principal (BdvMain) carregue as relações necessárias
        $record = $this->getRecord()->loadMissing('veiculo.modelo'); // <<< ADICIONE/AJUSTE ESTA LINHA

        // 1. Popular 'numero_rodas_veiculo' para visibilidade condicional e placeholder
        $data['numero_rodas_veiculo'] = $record->veiculo->modelo->numero_rodas ?? 0;

        // 2. Carregar e formatar dados do BdvRegistroMotorista
        $firstRegistroMotorista = $record->registrosMotorista()->first(); // Assumindo o primeiro registro de motorista

        if ($firstRegistroMotorista) {
            $data['bdv_registro_motorista'] = $firstRegistroMotorista->toArray();

            // Formatar momento_saida para o DateTimePicker
            if ($firstRegistroMotorista->momento_saida instanceof Carbon) {
                // Use o formato padrão interno do Filament/Livewire (Ano-Mês-Dia com hífens)
                $data['bdv_registro_motorista']['momento_saida'] = $firstRegistroMotorista->momento_saida->format('Y-m-d H:i:s');
            }

            // 3. Carregar e formatar dados do BdvItemStatus (Saída)
            $saidaStatus = $firstRegistroMotorista->itemStatus()
                ->where('tipo_registro', TipoRegistroStatusEnum::SAIDA)
                ->first();

            if ($saidaStatus) {
                foreach (BdvItemStatus::BOOLEAN_FIELDS as $field) {
                    $data['bdv_item_status_saida'][$field] = $saidaStatus->$field;
                }
            }

            // 4. Carregar e formatar dados do BdvItemStatus (Chegada) se existirem
            $chegadaStatus = $firstRegistroMotorista->itemStatus()
                ->where('tipo_registro', TipoRegistroStatusEnum::CHEGADA)
                ->first();
            if ($chegadaStatus) {
                foreach (BdvItemStatus::BOOLEAN_FIELDS as $field) {
                    $data['bdv_item_status_chegada'][$field] = $chegadaStatus->$field;
                }
            }

            // O Filament preencherá automaticamente os campos relacionados de BDVRegistroMotorista
            // e BDVItemStatus com base na estrutura que você forneceu em form().
        }

        return $data;
    }


    protected function afterSave(): void
    {
        $formData = $this->form->getState();

        DB::beginTransaction();

        try {
            $bdvMain = $this->getRecord();
            $registroMotorista = $bdvMain->registrosMotorista()->first();

            if ($registroMotorista) {
                $registroMotoristaData = $formData['bdv_registro_motorista'];

                // Manipular momento_saida para evitar InvalidFormatException
                $momentoSaida = $registroMotoristaData['momento_saida'] ?? null;
                if (!empty($momentoSaida)) {
                    $momentoSaidaCarbon = Carbon::createFromFormat('Y-m-d H:i:s', $momentoSaida);
                } else {
                    // Se o campo for obrigatório, a validação do formulário deve pegar.
                    // Se for opcional, defina como null.
                    $momentoSaidaCarbon = null;
                }

                // Usar fill() e save() para permitir que as regras de validação do modelo sejam acionadas
                $registroMotorista->fill(array_merge($registroMotoristaData, [
                    'momento_saida'  => $momentoSaidaCarbon,
                    'atualizado_por' => Auth::id(),
                ]))->save(); // .save() aciona eventos e validação do modelo

                // Processar BdvItemStatus (Saída)
                $itemStatusSaida = $registroMotorista->itemStatus()
                    ->where('tipo_registro', TipoRegistroStatusEnum::SAIDA)
                    ->first();

                $itemStatusSaidaData = $formData['bdv_item_status_saida'];

                if ($itemStatusSaida) {
                    $itemStatusSaida->fill(array_merge($itemStatusSaidaData, [
                        'atualizado_por' => Auth::id(),
                    ]))->save();
                } else {
                    BdvItemStatus::create(array_merge($itemStatusSaidaData, [
                        'id_registro_motorista' => $registroMotorista->id_registro_motorista,
                        'tipo_registro'         => TipoRegistroStatusEnum::SAIDA,
                        'cadastrado_por'        => Auth::id(),
                        'atualizado_por'        => Auth::id(),
                    ]));
                }
            }

            DB::commit();

            // Exibir uma notificação de sucesso, se a validação passou.
            Notification::make()
                ->title('Boletim Diário de Veículo salvo com sucesso!')
                ->success()
                ->send();

        } catch (ValidationException $e) {
            DB::rollBack();
            // O Filament já lida com ValidationException automaticamente e as exibe no formulário.
            // Re-lançá-la aqui garante que o Filament a capture e exiba os erros apropriadamente.
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Erro inesperado ao salvar BDV: " . $e->getMessage(), ['exception' => $e, 'formData' => $formData]);

            // Exibir uma notificação de erro amigável ao usuário
            Notification::make()
                ->title('Ocorreu um erro inesperado!')
                ->body('Não foi possível salvar o Boletim Diário de Veículo. Por favor, tente novamente. Se o problema persistir, contate o suporte. (ID do erro: ' . Str::random(8) . ')')
                ->danger()
                ->send();

            // Opcional: Se você quiser que o Livewire/Filament mostre a página de erro 500 padrão,
            // re-lance a exceção. Se preferir manter o usuário na página do formulário
            // e mostrar apenas a notificação, REMOVA 'throw $e;'.
            throw $e; // Mantido para que o servidor logue um 500 se for um erro crítico.
        }
    }
}
