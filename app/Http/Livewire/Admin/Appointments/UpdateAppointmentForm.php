<?php

namespace App\Http\Livewire\Admin\Appointments;

use App\Models\Client;
use App\Models\Appointment;
use Illuminate\Support\Facades\Validator;
use App\Http\Livewire\Admin\AdminComponent;

class UpdateAppointmentForm extends AdminComponent
{
    public $state = [];

    public $appointment;

    public function mount(Appointment $appointment)
    {
        $this->state = $appointment->toArray();

        $this->appointment = $appointment;
    }

    public function updateAppointment()
    {
		Validator::make(
			$this->state,
			[
				'client_id' => 'required',
                'members' => 'required',
				'date' => 'required|date',
				'time' => 'required',
				'note' => 'nullable',
				'status' => 'required|in:SCHEDULED,CLOSED',
			],
			[
				'client_id.required' => 'The client field is required.'
			])->validate();

		$this->appointment->update($this->state);

		$this->alert('success', 'Appointment updated Successfully ', [
            'position' => 'center',
            'background' => '#e6fff7'
        ]);

        return redirect()->route('admin.appointments');
    }

    public function render()
    {
        $clients = Client::all();

        return view('livewire.admin.appointments.update-appointment-form',[
            'clients' => $clients,
        ]);
    }
}
