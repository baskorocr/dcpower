<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReportsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $distributors;

    public function __construct($distributors)
    {
        $this->distributors = $distributors;
    }

    public function collection()
    {
        return $this->distributors;
    }

    public function headings(): array
    {
        return [
            'Code',
            'Distributor Name',
            'Project',
            'City',
            'Stock Count',
            'Sold Count',
            'Retail Count',
        ];
    }

    public function map($distributor): array
    {
        return [
            $distributor->code,
            $distributor->name,
            $distributor->project->name,
            $distributor->city,
            $distributor->stock_count,
            $distributor->sold_count,
            $distributor->retail_count,
        ];
    }
}
