<?php

namespace App\Http\Livewire\Admin\Appointments;

use App\Models\Client;
use App\Models\Appointment;
use Illuminate\Support\Facades\Validator;
use App\Http\Livewire\Admin\AdminComponent;

class CreateAppointmentForm extends AdminComponent
{
    public $state = [
		'status' => 'SCHEDULED',
	];

    public function createAppointment()
	{
        //dd($this->state);
		Validator::make(
			$this->state,
			[
				'client_id' => 'required',
				'members' => 'required',
				'color' => 'required',
				'date' => 'required|date',
				'time' => 'required',
				'note' => 'nullable',
				'status' => 'required|in:SCHEDULED,CLOSED',
			],
			[
				'client_id.required' => 'The client field is required.'
			])->validate();

		Appointment::create($this->state);

		$this->alert('success', 'New Appointment Added Successfully ', [
            'position' => 'center',
            'background' => '#e6fff7'
        ]);

        return redirect()->route('admin.appointments');
	}

    public function render()
    {
        $clients = Client::all();

        return view('livewire.admin.appointments.create-appointment-form', [
        	'clients' => $clients,
        ]);
    }
}
