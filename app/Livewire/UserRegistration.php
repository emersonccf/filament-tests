<?php

namespace App\Livewire;

use App\Mail\WelcomeEmail;
use App\Rules\CpfValido;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
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

        $user = User::create([
            'name' => trim($this->pessoa->nome),
            'cpf' => $this->cpf,
            'email' => $this->email ?: "{$this->cpf}@faker.com",
            'password' => Hash::make($this->password),
            'is_active' => true,
//            'is_admin' => false,
//            'belongs_sector' => false,
        ]);

        if (Str::endsWith($user->email, '@faker.com')) {
            // Redirecionar para uma página especial para usuários com e-mail faker
            session()->flash('message', 'Sua conta foi criada, mas precisa ser ativada pelo administrador do sistema.');
            return redirect()->route('admin.activation.required');
        } else {
            try {
                Mail::to($user->email)->send(new WelcomeEmail($user));
                $user->sendEmailVerificationNotification();
                Log::channel('email')->info("E-mail de boas-vindas e verificação enviado com sucesso para: {$user->email}");
            } catch (Exception $e) {
                Log::channel('email')->error('Falha ao enviar e-mail: ' . $e->getMessage());
            }

            //session()->flash('message', 'Conta criada com sucesso! Por favor, verifique seu e-mail para ativar sua conta.');
            return redirect()->route('verification.notice')->with('message', 'Conta criada com sucesso! Por favor, verifique seu e-mail para ativar sua conta.');
        }
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
