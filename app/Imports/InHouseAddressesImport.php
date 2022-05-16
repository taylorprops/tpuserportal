<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Marketing\InHouseAddresses;
use Maatwebsite\Excel\Concerns\WithStartRow;

class InHouseAddressesImport implements ToModel, WithStartRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new InHouseAddresses([
            'first_name'     => $row[0],
            'last_name'     => $row[1],
            'email'     => $row[2],
            'cell_phone'     => $row[3],
            'street'     => $row[4],
            'city'     => $row[5],
            'state'     => $row[6] == 'Maryland' ? 'MD' : $row[6],
            'zip'     => $row[7],
            'company'     => $row[8],
            'start_date'     => date('Y-m-d', strtotime($row[9])),
            'fullname' => $row[0].' '.$row[1],
        ]);
    }

    public function startRow(): int
    {
        return 2;
    }
}
