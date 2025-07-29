<?php

namespace App\Filament\Sevop\Resources\BdvMainResource\Pages;

use App\Enums\TipoRegistroStatusEnum;
use App\Enums\TipoTurnoEnum;
use App\Filament\Sevop\Resources\BdvMainResource;
use App\Models\BdvMain;
use App\Models\BdvItemStatus;
use App\Models\BdvRegistroMotorista;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Database\UniqueConstraintViolationException;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use App\Models\Veiculo; // Importado para obter dados do veículo

class CreateBdvMain extends CreateRecord
{
    protected static string $resource = BdvMainResource::class;
    protected static ?string $title = 'Novo Boletim Diário de Veículo';

    // Propriedades para armazenar dados temporários dos registros aninhados
    protected array $registroMotoristaData = [];
    protected array $itemStatusSaidaData = [];

    // Removi a anotação @override pois o método mutarteFormDataBeforeCreate não faz parte do ciclo de vida do CreateRecord.
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Atribui o usuário logado para o campo cadastrado_por
        $data['cadastrado_por'] = Auth::id();

        // Armazena os dados específicos do BdvRegistroMotorista e BdvItemStatus (Saida)
        // para serem usados no afterCreate, após o BdvMain ser salvo.
        $this->registroMotoristaData = $data['bdv_registro_motorista'];
        $this->itemStatusSaidaData = $data['bdv_item_status_saida'];

        // Remove esses dados do array principal para que o BdvMain possa ser criado pelo Filament
        unset($data['bdv_registro_motorista']);
        unset($data['bdv_item_status_saida']);
        unset($data['numero_rodas_veiculo']); // Campo auxiliar do formulário

        // Retorna apenas os dados do BdvMain para o Filament salvar.
        return $data;
    }

    // NOVO MÉTODO: Executa após o registro principal (BdvMain) ser salvo
    protected function afterCreate(): void
    {
        // Certifica-se de que temos o registro BdvMain recém-criado
        $bdvMain = $this->getRecord();

        DB::beginTransaction();
        try {
            // 1. Preparar momento_saida corretamente do DateTimePicker
            $momentoSaidaCarbon = Carbon::createFromFormat('Y-m-d H:i:s', $this->registroMotoristaData['momento_saida']);

            // 2. Cria o registro BdvRegistroMotorista
            $registroMotorista = BdvRegistroMotorista::create([
                'id_bdv' => $bdvMain->id_bdv, // Agora o id_bdv está disponível
                'id_condutor' => $this->registroMotoristaData['id_condutor'],
                'tipo_turno' => $this->registroMotoristaData['tipo_turno'],
                'momento_saida' => $momentoSaidaCarbon,
                'km_saida' => $this->registroMotoristaData['km_saida'],
                'nivel_combustivel_saida' => $this->registroMotoristaData['nivel_combustivel_saida'],
                'observacoes_saida' => $this->registroMotoristaData['observacoes_saida'] ?? null,
                'id_encarregado_saida' => $this->registroMotoristaData['id_encarregado_saida'],
                'cadastrado_por' => Auth::id(),
                'atualizado_por' => Auth::id(),
            ]);

            // 3. Cria o registro BdvItemStatus para a Saída
            $this->itemStatusSaidaData['id_registro_motorista'] = $registroMotorista->id_registro_motorista;
            $this->itemStatusSaidaData['tipo_registro'] = TipoRegistroStatusEnum::SAIDA;
            $this->itemStatusSaidaData['cadastrado_por'] = Auth::id();
            $this->itemStatusSaidaData['atualizado_por'] = Auth::id();

            BdvItemStatus::create($this->itemStatusSaidaData);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            // Se algo der errado aqui, precisamos reverter o BDV principal também
            // ou lidar com a exceção para notificar o usuário que a criação falhou parcialmente.
            // Para simplicidade, vamos lançar novamente para que o Filament lide com o erro.
            throw $e;
        }
    }


    protected function getRedirectUrl(): string
    {
        // Após a criação, redireciona para a página de visualização do BDV criado
        // Ajustei o TODO para redirecionar para a lista após a criação bem-sucedida,
        // mas você pode preferir a visualização do registro recém-criado.
        return $this->getResource()::getUrl('index'); // Redireciona para a lista de BDVs
        // Se preferir ir para a visualização do BDV criado, use:
        // return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    // MÉTODO CREATE SOBRESCRITO PARA TRATAMENTO DE ERROS (mantenha como está)
    public function create(bool $another = false): void
    {
        try {
            // Tenta criar o registro normalmente, chamando o método create da classe pai
            // Este método irá chamar mutateFormDataBeforeCreate e então salvar o BdvMain.
            parent::create($another);
        } catch (UniqueConstraintViolationException $e) {
            // Verifica se a exceção é especificamente da restrição de unicidade do BDV principal
            if (str_contains($e->getMessage(), 'bdv_main_id_veiculo_data_referencia_unique')) {
                $formData = $this->form->getState();

                $veiculoId = $formData['id_veiculo'] ?? null;
                // A data_referencia já é um objeto Carbon, mas pegamos o formato da string original para a mensagem
                $dataReferenciaFormString = $formData['data_referencia'] instanceof Carbon
                    ? $formData['data_referencia']->format('d/m/Y')
                    : $formData['data_referencia'];


                $veiculo = null;
                $existingBdv = null;

                if ($veiculoId && $dataReferenciaFormString) {
                    $existingBdv = BdvMain::where('id_veiculo', $veiculoId)
                        ->whereDate('data_referencia', $dataReferenciaFormString)
                        ->first();
                    $veiculo = Veiculo::find($veiculoId);
                }

                $veiculoInfo = '';
                if ($veiculo) {
                    $veiculoInfo = " (Placa: {$veiculo->placa}, Prefixo: {$veiculo->prefixo_veiculo})";
                }
                $dataInfo = $dataReferenciaFormString ? " em " . $dataReferenciaFormString : '';

                $notification = Notification::make()
                    ->title('BDV Já Cadastrado!')
                    ->body("Já existe um Boletim Diário de Veículo (BDV){$veiculoInfo} para a data{$dataInfo}.")
                    ->danger()
                    ->persistent();

                if ($existingBdv) {
                    $notification->actions([
                        Action::make('visualizar_existente')
                            ->label('Visualizar BDV Existente')
                            ->url(BdvMainResource::getUrl('view', ['record' => $existingBdv->id_bdv]), shouldOpenInNewTab: true)
                            ->button(),
                        Action::make('editar_existente')
                            ->label('Editar BDV Existente')
                            ->url(BdvMainResource::getUrl('edit', ['record' => $existingBdv->id_bdv]), shouldOpenInNewTab: true),
                    ]);
                } else {
                    $notification->body($notification->getBody() . " Por favor, tente com um veículo ou data diferente.");
                }

                $notification->send();
                $this->halt();

            } else {
                throw $e;
            }
        } catch (\Throwable $e) {
            // <<<<<<<<<<<<<<<< ADICIONE ESTA LINHA TEMPORARIAMENTE >>>>>>>>>>>>>>>>>>
            //dd($e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString());
            // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
            Notification::make()
                ->title('Ocorreu um erro inesperado!')
                ->body('Não foi possível criar o Boletim Diário de Veículo. Por favor, tente novamente. Se o problema persistir, contate o suporte. Detalhes: ' . $e->getMessage())
                ->danger()
                ->persistent()
                ->send();

            $this->halt();
        }
    }
}
