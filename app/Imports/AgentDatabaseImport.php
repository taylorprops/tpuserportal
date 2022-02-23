<?php

namespace App\Imports;

use App\Models\HeritageFinancial\AgentDatabase;
use Maatwebsite\Excel\Concerns\ToModel;

class AgentDatabaseImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new AgentDatabase([
            'first_name'     => $row[0],
            'last_name'     => $row[1],
            'email'     => $row[2],
            'cell_phone'     => $row[3],
            'street'     => $row[4],
            'city'     => $row[5],
            'state'     => $row[6],
            'zip'     => $row[7],
            'company'     => $row[8],
            'start_date'     => $row[9],
            'fullname' => $row[0].' '.$row[1]
        ]);
    }
}
