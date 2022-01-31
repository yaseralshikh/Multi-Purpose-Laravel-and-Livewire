<?php

use Illuminate\Support\Facades\Route;
use App\Http\Livewire\Admin\Users\ListUsers;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Livewire\Admin\Appointments\ListAppointments;
use App\Http\Livewire\Admin\Appointments\CreateAppointmentForm;
use App\Http\Livewire\Admin\Appointments\UpdateAppointmentForm;

Route::get('/', function () {
    return view('welcome');
});

Route::get('admin/dashboard', DashboardController::class)->name('admin.dashboard');

Route::get('admin/users', ListUsers::class)->name('admin.users');

Route::get('admin/appointments', ListAppointments::class)->name('admin.appointments');

Route::get('admin/appointments/create', CreateAppointmentForm::class)->name('admin.appointments.create');

Route::get('admin/appointments/{appointment}/edit', UpdateAppointmentForm::class)->name('admin.appointments.edit');

// Route::delete('admin/appointments/{appointment}/delete', UpdateAppointmentForm::class)->name('admin.appointments.delete');
