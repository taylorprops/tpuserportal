<?php

namespace App\Http\Controllers\HeritageFinancial;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Imports\AgentDatabaseImport;
use App\Models\HeritageFinancial\AgentDatabase;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AgentDatabaseController extends Controller
{
    public function agent_database(Request $request)
    {
        return view('/heritage_financial/agent_database/agent_database');
    }

    public function get_agent_database(Request $request)
    {
        $direction = $request -> direction ? $request -> direction : 'asc';
        $sort = $request -> sort ? $request -> sort : 'last_name';
        $length = $request -> length ? $request -> length : 25;
        $start_date = $request -> start_date ?? null;
        $end_date = $request -> end_date ?? null;
        $date_col = $request -> date_col ?? null;
        $search = $request -> search ?? null;

        $agents = AgentDatabase::select(['first_name', 'last_name', 'street', 'city', 'state', 'zip', 'email', 'cell_phone', 'start_date', 'company'])
        -> where(function($query) use ($search) {
            if($search) {
                $query -> where('fullname', 'like', '%'.$search.'%');
            }
        })
        -> where(function ($query) use ($date_col, $start_date, $end_date) {
            if ($date_col != null) {
                if ($this -> validate_date($start_date)) {
                    $query -> where($date_col, '>=', $start_date);
                }
                if ($this -> validate_date($end_date)) {
                    $query -> where($date_col, '<=', $end_date);
                }
            }
        })
        -> orderBy($sort, $direction);

        if ($request -> to_excel == 'false') {
            $agents = $agents -> paginate($length);

            return view('/heritage_financial/agent_database/get_agent_database_html', compact('agents'));
        } else {

            $select = ['First Name', 'Last Name', 'Street', 'City', 'State', 'Zip', 'Email', 'Cell Phone', 'Start Date', 'Company'];
            $agents = $agents -> get()
            -> toArray();

            $filename = 'agents_'.time().'.xlsx';
            $file = Helper::to_excel($agents, $filename, $select);

            return response() -> json(['file' => $file]);
        }
    }

    public function add_new_list(Request $request)
    {
        AgentDatabase::truncate();

        $file = $request -> file('agent_list');

        Excel::import(new AgentDatabaseImport, $file);
    }

    public function validate_date($date, $format = 'Y-m-d')
    {
        $d = \DateTime::createFromFormat($format, $date);

        return $d && $d -> format($format) == $date;
    }
}
