<?php

namespace App\Exports;

use App\Models\WarrantyClaim;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Http\Request;

class ClaimHistoryExport implements FromQuery, WithHeadings, WithMapping
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $query = WarrantyClaim::with(['product.project', 'claimedBy', 'approver'])
            ->whereHas('product'); // Only export claims with existing products

        if ($this->request->filled('status')) {
            $query->where('status', $this->request->status);
        }

        if ($this->request->filled('date_from')) {
            $query->whereDate('submitted_at', '>=', $this->request->date_from);
        }
        if ($this->request->filled('date_to')) {
            $query->whereDate('submitted_at', '<=', $this->request->date_to);
        }

        if ($this->request->filled('search')) {
            $search = $this->request->search;
            $query->where(function($q) use ($search) {
                $q->where('claim_number', 'like', "%{$search}%")
                  ->orWhereHas('product', function($q2) use ($search) {
                      $q2->where('serial_number', 'like', "%{$search}%");
                  });
            });
        }

        return $query->latest('submitted_at');
    }

    public function headings(): array
    {
        return [
            'Claim Number',
            'Serial Number',
            'Product',
            'Variant',
            'Type',
            'Status',
            'Submitted At',
            'Claimed By'
        ];
    }

    public function map($claim): array
    {
        return [
            $claim->claim_number,
            $claim->product->serial_number,
            $claim->product->project->name ?? 'N/A',
            $claim->product->variant ?? '-',
            ucfirst($claim->complaint_type),
            ucfirst($claim->status),
            $claim->submitted_at->format('d M Y H:i'),
            $claim->claimedBy->name ?? 'N/A'
        ];
    }
}
