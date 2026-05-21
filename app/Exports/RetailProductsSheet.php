<?php

namespace App\Exports;

use App\Models\Product;
use App\Models\Retail;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RetailProductsSheet implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected $retail;

    public function __construct(Retail $retail)
    {
        $this->retail = $retail;
    }

    public function collection()
    {
        return Product::whereHas('stockMovements', function($q) {
                $q->where('retail_id', $this->retail->id);
            })
            ->with('project')
            ->get()
            ->map(function($product) {
                return [
                    'serial_number' => $product->serial_number,
                    'project' => $product->project->name ?? 'N/A',
                    'variant' => $product->variant ?? 'N/A',
                    'status' => ucfirst(str_replace('_', ' ', $product->status)),
                    'manufactured' => $product->created_at->format('d M Y'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Serial Number',
            'Project',
            'Variant',
            'Status',
            'Manufactured',
        ];
    }

    public function title(): string
    {
        return substr($this->retail->name, 0, 31);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
