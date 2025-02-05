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

    public function updatedPhoto()
    {
        $this->validate([
            'photo' => 'image|max:1024', // 1MB Max
        ]);
    }

    public function save()
    {
        $this->validate([
            'photo' => 'required|image|max:1024', // 1MB Max
        ]);

        $user = Auth::user();

        if ($this->photo) {
            // Generate a unique filename
            $filename = 'profile-' . $user->id . '-' . time() . '.' . $this->photo->getClientOriginalExtension();

            // Store the file
            $path = $this->photo->storeAs('profile-photos', $filename, 'public');

            if ($path) {
                // Delete the old photo if it exists
                if ($user->profile_photo_path) {
                    Storage::disk('public')->delete($user->profile_photo_path);
                }

                // Update user's profile photo path
                $user->update([
                    'profile_photo_path' => $path,
                ]);

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

    public function render()
    {
        return view('livewire.form.profile-photo-uploader');
    }
}
