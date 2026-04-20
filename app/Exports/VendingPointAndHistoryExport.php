<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use DB;

class VendingPointAndHistoryExport implements WithMultipleSheets, ShouldAutoSize
{
    use Exportable;

    protected $request;

    function __construct($request) {

        $this->request = $request;
    }

    /**
     * @return array
     */ 
    public function sheets(): array
    {

        $sheets = [

            new VendingPointExport($this->request),
            new VendingPointCommunitiyExport($this->request),
            new VendingHistoryExport($this->request),
        ];

        return $sheets;
    }
}