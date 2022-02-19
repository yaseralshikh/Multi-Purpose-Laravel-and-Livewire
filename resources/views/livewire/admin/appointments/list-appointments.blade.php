<div>
    <x-loading-indicator />

    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Appointments</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Appointments</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex justify-content-between mb-2">
                        <div>
                            <a href="{{ route('admin.appointments.create') }}">
                                <button class="btn btn-primary btn-sm"><i class="fa fa-plus-circle mr-1"></i> Add New
                                    Appointment</button>
                            </a>

                            @if ($selectedRows)
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default">Bulk Actions</button>
                                    <button type="button" class="btn btn-default dropdown-toggle dropdown-icon"
                                        data-toggle="dropdown" aria-expanded="false">
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <div class="dropdown-menu" role="menu" style="">
                                        <a wire:click.prevent="deleteSelectedRows" class="dropdown-item" href="#">Delete Selected</a>
                                        <a wire:click.prevent="markAllAsScheduled" class="dropdown-item" href="#">Mark as Scheduled</a>
                                        <a wire:click.prevent="markAllAsClosed" class="dropdown-item" href="#">Mark as Close</a>
                                        <div class="dropdown-divider"></div>
                                        <a wire:click.prevent="export" class="dropdown-item" href="#">Export to Excel</a>
                                    </div>
                                </div>
                                <div class="d-inline-block animate__animated animate__pulse animate__infinite">
                                    <span class="ml-2">Selected <span class="text-primary">{{ count($selectedRows) }}</span> {{ Str::plural('appointmen', count($selectedRows)) }}</span>
                                </div>

                            @endif
                        </div>

                        <div class="btn-group">
                            <button wire:click="filterAppointmentsByStatus" type="button"
                                class="btn {{ is_null($status) ? 'btn-secondary' : 'btn-default' }}">
                                <span class="mr-1">All</span>
                                <span class="badge badge-pill badge-info">{{ $appointmentsCount }}</span>
                            </button>

                            <button wire:click="filterAppointmentsByStatus('scheduled')" type="button"
                                class="btn {{ $status === 'scheduled' ? 'btn-secondary' : 'btn-default' }}">
                                <span class="mr-1">Scheduled</span>
                                <span class="badge badge-pill badge-primary">{{ $scheduledAppointmentsCount }}</span>
                            </button>

                            <button wire:click="filterAppointmentsByStatus('closed')" type="button"
                                class="btn {{ $status === 'closed' ? 'btn-secondary' : 'btn-default' }}">
                                <span class="mr-1">Closed</span>
                                <span class="badge badge-pill badge-success">{{ $closedAppointmentsCount }}</span>
                            </button>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover text-center">
                                    <thead>
                                        <tr class="bg-light">
                                            <th></th>
                                            <th>
                                                <div class="icheck-primary d-inline ml-2">
                                                    <input wire:model="selectPageRows" type="checkbox" value="" name="todo2" id="todoCheck2">
                                                    <label for="todoCheck2"></label>
                                                </div>
                                            </th>
                                            <th scope="col">#</th>
                                            <th scope="col">Name Client</th>
                                            <th scope="col">Date</th>
                                            <th scope="col">Time</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Option</th>
                                        </tr>
                                    </thead>
                                    <tbody wire:sortable="updateAppointmentOrder">
                                        @foreach ($appointments as $appointment)
                                            <tr wire:sortable.item="{{ $appointment->id }}" wire:key="task-{{ $appointment->id }}">
                                                <td wire:sortable.handle style="width: 10px; cursor: move;"><i class="fa fa-arrows-alt text-muted"></i></td>
                                                <th style="width: 10px;">
                                                    <div class="icheck-primary d-inline">
                                                        <input wire:model="selectedRows" type="checkbox" value="{{ $appointment->id }}" name="todo2"
                                                            id="{{ $appointment->id }}">
                                                        <label for="{{ $appointment->id }}"></label>
                                                    </div>
                                                </th>
                                                <th scope="row">
                                                    {{ $loop->iteration + $appointments->firstItem() - 1 }}</th>
                                                <td>{{ $appointment->client->name }}</td>
                                                <td> <i class="fa fa-solid fa-calendar" style="color: {{ $appointment->color }}"></i> {{ $appointment->date }}</td>
                                                <td>{{ $appointment->time }}</td>
                                                <td>
                                                    <span
                                                        class="badge badge-{{ $appointment->status_badge }}">{{ $appointment->status }}</span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.appointments.edit', $appointment) }}">
                                                        <i class="fa fa-edit mr-2"></i>
                                                    </a>
                                                    <a href="#"
                                                        wire:click.prevent="confirmAppointmentRemoval({{ $appointment->id }})">
                                                        <i class="fa fa-trash text-danger"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                {{-- @dump($selectedRows) --}}
                            </div>

                        </div>
                        <div class="card-footer d-flex justify-content-end">
                            {!! $appointments->links() !!}
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->

    <x-confirmation-alert />
</div>

@push('styles')
    <style>
        .draggable-mirror {
            background-color: white;
            width: 950px;
            display: flex;
            justify-content: space-between;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
    </style>
@endpush
