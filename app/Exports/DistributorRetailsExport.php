<?php

namespace App\Exports;

use App\Models\Distributor;
use App\Models\Product;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DistributorRetailsExport implements WithMultipleSheets
{
    protected $distributor;

    public function __construct(Distributor $distributor)
    {
        $this->distributor = $distributor;
    }

    public function sheets(): array
    {
        $sheets = [];
        
        $retails = $this->distributor->retails;
        
        foreach ($retails as $retail) {
            $sheets[] = new RetailProductsSheet($retail);
        }
        
        return $sheets;
    }
}
