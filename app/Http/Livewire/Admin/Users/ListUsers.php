<?php

namespace App\Http\Livewire\Admin\Users;

use App\Models\User;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Illuminate\Support\Facades\Validator;
use Livewire\WithPagination;

class ListUsers extends Component
{
    use LivewireAlert;
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $state =[];
    public $user;
    public $showEditModal = false;

    public $userIdBeingRemoved = null;

    public function addNew()
    {
        $this->showEditModal = false;
        $this->state = [];
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
        $users = User::latest()->paginate(3);
        return view('livewire.admin.users.list-users',[
            'users' => $users,
        ]);
    }
}
