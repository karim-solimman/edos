<?php

namespace App\Exports;

use App\Models\Inv;
use Maatwebsite\Excel\Concerns\FromCollection;

class InvsExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Inv::all();
    }
}
