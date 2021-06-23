<?php

namespace App\Http\Controllers\Resources;

use Illuminate\Http\Request;
use App\Models\Config\Config;
use App\Http\Controllers\Controller;

class ResourcesController extends Controller
{
    public function form_elements(Request $request) {

        return view('resources/design/form_elements');

    }

    public function config_variables(Request $request) {

        return view('resources/config/config_variables');

    }

    public function get_config_variables(Request $request) {

        $config = Config::get();

        return datatables() -> of($config)
        -> editColumn('config_key', function($config) {
            return '<span class="font-semibold">'.$config -> config_key.'</span>';
        })
        -> editColumn('config_value', function($config) {
            return '<textarea class="config-value w-full" rows="'.(strlen($config -> config_value) / 110).'"
            @change="
                let value = $event.currentTarget.value;
                $fetch({
                    url: \'/resources/config/config_edit\',
                    method: \'POST\',
                    params: {
                        \'id\': \''.$config -> id.'\',
                        \'field\': \'config_value\',
                        \'value\': value
                    }
                })
                .then(function(){
                    toastr.success(\'Config updated\');
                });
            "
            >'.$config -> config_value.'</textarea>';
        })
        -> editColumn('value_type', function($config) {
            $string = '';
            $array = '';
            if($config -> value_type == 'string') {
                $string = 'selected';
            } else {
                $array = 'selected';
            }
            return '
            <select class="config-key" data-id="'.$config -> id.'"
            @change="
                let value = $event.currentTarget.value;
                $fetch({
                    url: \'/resources/config/config_edit\',
                    method: \'POST\',
                    params: {
                        \'id\': \''.$config -> id.'\',
                        \'field\': \'value_type\',
                        \'value\': value
                    }
                })
                .then(function(){
                    toastr.success(\'Config updated\');
                });
            "
            >
                <option value="string" '.$string.'>String</option>
                <option value="array" '.$array.'>Array</option>
            </select>';

        })
        -> escapeColumns([])
        -> make(true);

    }

    public function config_edit(Request $request) {

        $update_config = Config::find($request -> id) -> update([
            $request -> field => $request -> value
        ]);

        return response() -> json(['status' => 'success']);

    }

    public function config_add(Request $request) {

        $add_config = Config::create([
            'config_key' => $request -> config_key,
            'config_value' => $request -> config_value,
            'value_type' => $request -> value_type
        ]);

    }
}
