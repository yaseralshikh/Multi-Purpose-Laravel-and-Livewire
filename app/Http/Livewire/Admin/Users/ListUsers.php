<?php

namespace App\Http\Livewire\Admin\Users;

use App\Models\User;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Livewire\Admin\AdminComponent;

class ListUsers extends AdminComponent
{
    use WithFileUploads;

    public $state =[];

    public $user;

    public $showEditModal = false;

    public $userIdBeingRemoved = null;

    public $searchTerm = null;

    public $photo;

    public function addNew()
    {
        $this->reset();

        $this->state = [];

        $this->showEditModal = false;

        $this->dispatchBrowserEvent('show-form');
    }

    public function createUser()
    {
        $validatedData = Validator::make($this->state, [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
            ])->validate();

        $validatedData['password'] = bcrypt($validatedData['password']);

        if ($this->photo) {
			$validatedData['avatar'] = $this->photo->store('/', 'avatars');
		}

        User::create($validatedData);

        $this->dispatchBrowserEvent('hide-form');

        $this->alert('success', 'New User Added Successfully ', [
            'position' => 'center',
            'background' => '#e6fff7'
        ]);

        return redirect()->back();
    }

    public function edit(User $user)
    {
        $this->reset();
        $this->showEditModal = true;
        $this->user = $user;
        $this->state = $user->toArray();
        $this->dispatchBrowserEvent('show-form');
    }

    public function updateUser()
    {
        $validatedData = Validator::make($this->state, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$this->user->id,
            'password' => 'sometimes|confirmed',
            ])->validate();

        if(!empty($validatedData['password'])){
            $validatedData['password'] = bcrypt($validatedData['password']);
        }

        if ($this->photo) {
			Storage::disk('avatars')->delete($this->user->avatar);
			$validatedData['avatar'] = $this->photo->store('/', 'avatars');
		}

        $this->user->update($validatedData);

        $this->dispatchBrowserEvent('hide-form');

        $this->alert('success', 'User updated Successfully ', [
            'position' => 'center',
            'background' => '#e6fff7'
        ]);
    }

    public function confirmUserRemoval($userId)
    {
        $this->userIdBeingRemoved = $userId;
        $this->dispatchBrowserEvent('show-delete-modal');

    }

    public function deleteUser()
    {
        $user = User::findOrFail($this->userIdBeingRemoved);
        $user->delete();

        $this->dispatchBrowserEvent('hide-delete-modal');

        $this->alert('success', 'User deleted Successfully ', [
            'position' => 'center',
            'background' => '#e6fff7'
        ]);
    }

    public function render()
    {
        $users = User::query()
            ->where('name', 'like', '%'.$this->searchTerm.'%')
            ->orWhere('email', 'like', '%'.$this->searchTerm.'%')
            ->latest()->paginate(10);

        return view('livewire.admin.users.list-users',[
            'users' => $users,
        ]);
    }
}
