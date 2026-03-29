<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AgricultureHolderCombinedExport implements WithMultipleSheets
{
    protected $request;

    public function __construct($request = null)
    {
        $this->request = $request;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        
        $sheets = [
            new AgricultureHolderExport($this->request),
            new AgricultureHolderSmartboardExport(),
        ];

        return $sheets;
    }
}
