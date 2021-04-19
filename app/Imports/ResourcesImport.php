<?php

namespace App\Imports;
use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ResourcesImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Supplier([
            "category"=>$row['category'],
            "name"=>$row['name'],
            "location"=>$row['location'],
            "phone"=>$row['phone']
        ]);
    }
}
