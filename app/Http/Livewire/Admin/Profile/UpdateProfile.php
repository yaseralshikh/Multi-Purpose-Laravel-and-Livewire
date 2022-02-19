<?php

namespace App\Http\Livewire\Admin\Profile;

use Illuminate\Support\Arr;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Http\Livewire\Admin\AdminComponent;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateProfile extends AdminComponent
{
    use WithFileUploads;

    public $image;

    public $state = [];

    public function mount()
    {
        $this->state = auth()->user()->only(['name', 'email']);
    }

    public function updatedImage()
    {
        $previousPath = auth()->user()->avatar;

        $path = $this->image->store('/', 'avatars');

        auth()->user()->update(['avatar' => $path]);

        Storage::disk('avatars')->delete($previousPath);

        $this->alert('success', 'Profile changed successfully!', [
            'position' => 'center',
            'background' => '#e6fff7'
        ]);
    }

    public function updateProfile(UpdatesUserProfileInformation $updater)
    {
        $updater->update(auth()->user(), [
            'name' => $this->state['name'],
            'email' => $this->state['email']
        ]);

        $this->emit('nameChanged', auth()->user()->name);

        $this->alert('success', 'Profile updated successfully!', [
            'position' => 'center',
            'background' => '#e6fff7'
        ]);
    }

    public function changePassword(UpdatesUserPasswords $updater)
    {
        $updater->update(
            auth()->user(),
            $attributes = Arr::only($this->state, ['current_password', 'password', 'password_confirmation'])
        );

        collect($attributes)->map(fn ($value, $key) => $this->state[$key] = '');

        $this->alert('success', 'Password changed successfully!', [
            'position' => 'center',
            'background' => '#e6fff7'
        ]);
    }

    public function render()
    {
        return view('livewire.admin.profile.update-profile');
    }
}
