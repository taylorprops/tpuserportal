<?php

namespace App\Http\Controllers\Resources;

use App\Http\Controllers\Controller;
use App\Models\Config\Config;
use Illuminate\Http\Request;

class ResourcesController extends Controller
{
    public function form_elements(Request $request)
    {
        return view('resources/design/form_elements');
    }

    public function config_variables(Request $request)
    {
        return view('resources/config/config_variables');
    }

    public function get_config_variables(Request $request)
    {
        $direction = $request->direction ? $request->direction : 'asc';
        $sort = $request->sort ? $request->sort : 'config_key';
        $length = $request->length ? $request->length : 10;

        $search = $request->search ?? null;

        $configs = Config::where(function ($query) use ($search) {
            if ($search) {
                $query->where('config_key', 'like', '%'.$search.'%');
            }
        })
        ->orderBy($sort, $direction)
        ->paginate($length);

        return view('resources/config/get_config_variables_html', compact('configs'));
    }

    public function config_edit(Request $request)
    {
        $update_config = Config::find($request->id)->update([
            $request->field => $request->value,
        ]);

        return response()->json(['status' => 'success']);
    }

    public function config_add(Request $request)
    {
        $add_config = Config::create([
            'config_key' => $request->config_key,
            'config_value' => $request->config_value,
            'value_type' => $request->value_type,
        ]);
    }
}
