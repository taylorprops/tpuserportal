<?php

namespace App\Imports;


use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use App\Models\Marketing\TestCenterAddressesTemp;

class TestCenterAddressesImport implements ToModel, WithStartRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new TestCenterAddressesTemp([
            'full_name' => $row[0],
            'first_name' => substr($row[0], 0, strpos($row[0], ' ')),
            'last_name' => trim(substr($row[0], strpos($row[0], ' '))),
            'street' => trim($row[1].' '.$row[2]),
            'city' => $row[3],
            'state' => $row[4],
            'zip' => $row[5],
            'phone' => $row[6],
            'email' => $row[7],
            'last_test_date' => date("Y-m-d", strtotime($row[8])),
            'test_name' => $row[9],
            'result' => $row[10],

        ]);
    }

    public function startRow(): int
    {
        return 2;
    }
}
