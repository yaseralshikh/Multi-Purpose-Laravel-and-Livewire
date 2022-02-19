<?php

namespace App\Exports;

use App\Models\Appointment;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AppointmentsExport implements FromQuery, WithMapping, WithHeadings, WithStyles
{
    use Exportable;

    protected $selectedRows;

    public function __construct($selectedRows)
    {
        $this->selectedRows = $selectedRows;
    }

    public function map($appointment): array
    {
        return [
            $appointment->id,
            $appointment->client->name,
            $appointment->date,
            $appointment->time,
            $appointment->status,
        ];
    }

    public function headings(): array
    {
        return [
            '# ID',
            'Client Name',
            'Date',
            'Time',
            'Status',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true, 'size' => 12, 'color' => array('rgb' => '0000FF')]],

            // Styling a specific cell by coordinate.
            //'B2' => ['font' => ['italic' => true]],

            // Styling an entire column.
            'A'  => ['font' => ['bold' => true, 'size' => 12, 'color' => array('rgb' => '0000FF')]],
        ];
    }

    public function query()
    {
        return Appointment::with('client:id,name')->whereIn('id', $this->selectedRows);
    }

}
