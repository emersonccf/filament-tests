<?php

namespace App\Livewire;

use App\Rules\CpfValido;
use Livewire\Component;
use App\Models\Pessoa;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRegistration extends Component
{
    public $cpf;
    public $pessoa;
    public $nomePessoa = '';
    public $dataNascimento;
    public $matricula;
    public $email;
    public $password;
    public $password_confirmation;
    public $step = 'cpf';
    public $message;
    public $titulo = 'CADASTRO DE USUÁRIO';

    protected function rules()
    {
        return [
            'cpf' => ['required', new CpfValido],
            'dataNascimento' => 'required|date',
            'matricula' => 'required',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ];
    }

    public function updatedCpf()
    {
        $this->validateOnly('cpf');
        $this->pessoa = Pessoa::where('cpf', $this->cpf)->first();

        if ($this->pessoa) {
            $this->nomePessoa = trim($this->pessoa->nome);
            $user = User::where('cpf', $this->cpf)->first();
            if ($user) {
                $this->step = 'user_exists';
                $this->message = 'Já exite uma conta de usuário no sistema associado a este CPF.';
            } else {
                $this->step = 'verify_data';
            }
        } else {
            $this->step = 'cpf_not_found';
            $this->message = 'CPF não localizado na base de dados.';
        }
    }

    public function verifyData()
    {
        $this->validateOnly('dataNascimento');
        $this->validateOnly('matricula');

        if ($this->pessoa->data_nascimento == $this->dataNascimento && $this->pessoa->matricula == $this->matricula) {
            $this->step = 'create_account';
        } else {
            $this->step = 'data_inconsistent';
            $this->message = 'Os dados informados são inconsistentes. Não é possível realizar o cadastro.';
        }
    }

    public function createAccount()
    {
        $this->validate();

        User::create([
            'name' => trim($this->pessoa->nome),
            'cpf' => $this->cpf,
            'email' => $this->email ?? $this->cpf.'@faker.com',
            'password' => Hash::make($this->password),
        ]);

        session()->flash('message', 'Conta criada com sucesso!');
        return redirect()->route('filament.adm.auth.login');
    }

    public function render()
    {
        return view('livewire.user-registration')->layout('components.layouts.app');
    }
}
