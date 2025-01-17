<?php

namespace App\Livewire;

use App\Rules\CpfValido;
use Livewire\Component;
use App\Models\Pessoa;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

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
    public $tituloPagina = 'CADASTRO DE USUÁRIO';
    public $tituloFormulario = 'REALIZE SEU CADASTRO AQUI';

    protected function rules()
    {
        return [
            'cpf' => ['required', new CpfValido],
            'dataNascimento' => 'required|date',
            'matricula' => 'required|numeric',
            'email' => ['nullable', 'email', Rule::unique('users', 'email')],
            'password' => 'required|min:8|confirmed',
        ];
    }

    protected $messages = [
        'matricula.required' => 'A matrícula é obrigatória.',
        'matricula.numeric' => 'A matrícula deve conter apenas números.',
        'email.unique' => 'Este e-mail já está vinculado a uma conta de usuário no sistema.',
        'dataNascimento.required' => 'A data de nascimento é obrigatória.',
    ];

    public function updatedCpf()
    {
        $this->validateOnly('cpf');
        $this->cpf = preg_replace('/[^0-9]/', '', $this->cpf);
        $this->pessoa = Pessoa::where('cpf', $this->cpf)->first();

        if ($this->pessoa) {
            $this->nomePessoa = trim($this->pessoa->nome);
            $this->step = User::where('cpf', $this->cpf)->exists() ? 'user_exists' : 'verify_data';
            $this->message = $this->step === 'user_exists' ? 'Já existe uma conta de usuário no sistema associada a este CPF.' : '';
        } else {
            $this->step = 'cpf_not_found';
            $this->message = 'CPF não localizado na base de dados.';
        }
    }

    public function verifyData()
    {
        $this->validateOnly('dataNascimento');
        $this->validateOnly('matricula');

        $isDataConsistent = $this->pessoa->data_nascimento == $this->dataNascimento && $this->pessoa->matricula == $this->matricula;

        $this->step = $isDataConsistent ? 'create_account' : 'data_inconsistent';
        $this->message = $isDataConsistent ? '' : 'Os dados informados são inconsistentes. Não é possível realizar o cadastro.';
        $this->nomePessoa = $isDataConsistent ? $this->nomePessoa : '';
    }

    public function createAccount()
    {
        $this->validate();

        User::create([
            'name' => trim($this->pessoa->nome),
            'cpf' => $this->cpf,
            'email' => $this->email ?: "{$this->cpf}@faker.com",
            'password' => Hash::make($this->password),
        ]);

        session()->flash('message', 'Conta criada com sucesso!');
        session()->flash('message_type', 'success');
        return redirect()->route('filament.adm.auth.login');
    }

    public function render()
    {
        return view('livewire.user-registration', [
            'step' => $this->step,
        ])->layout('components.layouts.app', [
            'tituloPagina' => $this->tituloPagina,
            'tituloFormulario' => $this->tituloFormulario,
        ]);
    }
}
