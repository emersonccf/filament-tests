<?php
//
//namespace App\Filament\Sevop\Resources\BdvMainResource\Pages;
//
//use App\Enums\TipoRegistroStatusEnum;
//use App\Enums\TipoTurnoEnum;
//use App\Filament\Sevop\Resources\BdvMainResource;
//use App\Models\BdvMain;
//use App\Models\BdvItemStatus;
//use App\Models\BdvRegistroMotorista;
//use Filament\Actions;
//use Filament\Resources\Pages\CreateRecord;
//use Illuminate\Support\Facades\Auth;
//use Illuminate\Support\Facades\DB;
//
//class CreateBdvMain extends CreateRecord
//{
//    protected static string $resource = BdvMainResource::class;
//    protected static ?string $title = 'Novo Boletim Diário de Veículo';
//
//    protected function mutateFormDataBeforeCreate(array $data): array
//    {
//        // Atribui o usuário logado para o campo cadastrado_por
//        $data['cadastrado_por'] = Auth::id();
//
//        // Extrai os dados específicos do BdvRegistroMotorista e BdvItemStatus (Saida)
//        $registroMotoristaData = $data['bdv_registro_motorista'];
//        $itemStatusSaidaData = $data['bdv_item_status_saida'];
//
//        // Remove esses dados do array principal para que o BdvMain possa ser criado
//        unset($data['bdv_registro_motorista']);
//        unset($data['bdv_item_status_saida']);
//        unset($data['numero_rodas_veiculo']); // Campo auxiliar do formulário
//
//        // Inicia uma transação de banco de dados para garantir a atomicidade
//        DB::beginTransaction();
//
//        try {
//            // 1. Cria o registro BdvMain
//            $bdvMain = BdvMain::create($data);
//
//            // 2. Cria o registro BdvRegistroMotorista para o primeiro turno (Matutino)
//            $registroMotorista = BdvRegistroMotorista::create([
//                'id_bdv' => $bdvMain->id_bdv,
//                'id_condutor' => $registroMotoristaData['id_condutor'],
//                'tipo_turno' => $registroMotoristaData['tipo_turno'], // ajuste para definir o turno
//                'momento_saida' => $bdvMain->data_referencia->setTimeFromDataTimeString($registroMotoristaData['momento_saida']),
//                'km_saida' => $registroMotoristaData['km_saida'],
//                'nivel_combustivel_saida' => $registroMotoristaData['nivel_combustivel_saida'],
//                'observacoes_saida' => $registroMotoristaData['observacoes_saida'] ?? null,
//                'id_encarregado_saida' => $registroMotoristaData['id_encarregado_saida'] ?? null,
//                'cadastrado_por' => Auth::id(),
//                'atualizado_por' => Auth::id(),
//                // Campos de chegada ficam NULL por padrão
//            ]);
//
//            // 3. Cria o registro BdvItemStatus para a Saída
//            $itemStatusSaidaData['id_registro_motorista'] = $registroMotorista->id_registro_motorista;
//            $itemStatusSaidaData['tipo_registro'] = TipoRegistroStatusEnum::SAIDA;
//            $itemStatusSaidaData['cadastrado_por'] = Auth::id();
//            $itemStatusSaidaData['atualizado_por'] = Auth::id();
//
//            BdvItemStatus::create($itemStatusSaidaData);
//
//            DB::commit();
//
//            // Retorna o BdvMain recém-criado para o Filament, que ele salvará
//            // O registro principal que o Filament espera é o BdvMain
//            return $bdvMain->toArray();
//
//        } catch (\Exception $e) {
//            DB::rollBack();
//            // Lançar a exceção novamente para o Filament exibir uma mensagem de erro
//            throw $e;
//        }
//    }
//
//    protected function getRedirectUrl(): string
//    {
//        // Após a criação, redireciona para a página de visualização do BDV criado
//        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
//    }
//}
