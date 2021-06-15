<?php

namespace App\Http\Controllers\Employees;

use Illuminate\Http\Request;
use App\Models\Employees\Agents;
use App\Http\Controllers\Controller;


class EmployeesController extends Controller
{

    public function agents(Request $request) {


        return view('/employees/agents/agents');

    }

    public function get_agents(Request $request) {

        $agents = Agents::select(['full_name', 'cell_phone', 'email']) -> get();

        $button_classes = 'px-3 py-2 text-sm bg-primary hover:bg-primary-dark active:bg-primary-dark focus:border-primary-dark ring-primary-dark inline-flex items-center rounded text-white shadow hover:shadow-lg outline-none tracking-wider focus:outline-none disabled:opacity-25 transition-all ease-in-out duration-150 shadow hover:shadow-md';

        return datatables() -> of($agents)
        -> addColumn('edit', function ($agents) use ($button_classes) {
            return '<a href="" class="'.$button_classes.'"><i class="fal fa-pencil fa-sm mr-2"></i> View/Edit</a>';
        })
        -> editColumn('full_name', function($agents) {
            return '<span class="text-red-500">'.$agents -> full_name.'</span>';
        })
        -> escapeColumns([])
        -> make(true);

    }

}
