<?php
// Resolvido uploads com problemas através de: https://stackoverflow.com/questions/70093647/path-cannot-be-empty-laravel

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfilePhotoUploader extends Component
{
    use WithFileUploads;

    public $photo;

    protected $rules = [
        'photo' => 'nullable|image|max:1024', // 1MB Max
    ];

    public function updatedPhoto()
    {
        $this->validateOnly('photo');
    }

    public function savePhoto()
    {
        $this->validate();

        $user = Auth::user();

        if ($this->photo) {
            $filename = 'profile-' . $user->id . '-' . time() . '.' . $this->photo->getClientOriginalExtension();
            $path = $this->photo->storeAs('profile-photos', $filename, 'public');

            if ($path) {
                if ($user->profile_photo_path) {
                    Storage::disk('public')->delete($user->profile_photo_path);
                }

                $user->update(['profile_photo_path' => $path]);

                $this->dispatch('profile-photo-updated');
                session()->flash('message', 'Foto de perfil atualizada com sucesso!');
            } else {
                session()->flash('error', 'Ocorreu um erro ao salvar a foto. Por favor, tente novamente.');
            }
        } else {
            session()->flash('error', 'Nenhuma foto foi selecionada.');
        }

        $this->reset('photo');
    }

    public function removePhoto()
    {
        $user = Auth::user();

        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
            $user->update(['profile_photo_path' => null]);
            $this->dispatch('photo:removed');
            session()->flash('message', 'Foto de perfil removida com sucesso!');
        } else {
            session()->flash('error', 'Não há foto de perfil para remover.');
        }
    }

    public function render()
    {
        return view('livewire.form.profile-photo-uploader');
    }
}
