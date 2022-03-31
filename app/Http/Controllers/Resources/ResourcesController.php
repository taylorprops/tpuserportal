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
        $direction = $request -> direction ? $request -> direction : 'asc';
        $sort = $request -> sort ? $request -> sort : 'config_key';
        $length = $request -> length ? $request -> length : 10;

        $search = $request -> search ?? null;

        $configs = Config::where(function ($query) use ($search) {
            if ($search) {
                $query -> where('config_key', 'like', '%'.$search.'%');
            }
        })
        -> orderBy($sort, $direction)
        -> paginate($length);

        return view('resources/config/get_config_variables_html', compact('configs'));
    }

    public function config_edit(Request $request)
    {

        $config = Config::find($request -> id);
        $find = $config -> config_key;
        $replace = $request -> value;

        $update_config = Config::find($request -> id) -> update([
            $request -> field => $request -> value,
        ]);

        $this -> replace_keys_in_files($find, $replace);

        return response() -> json(['status' => 'success']);
    }

    public function config_add(Request $request)
    {
        $add_config = Config::create([
            'config_key' => $request -> config_key,
            'config_value' => $request -> config_value,
            'value_type' => $request -> value_type,
        ]);
    }

    public function config_delete(Request $request) {

        $config = Config::find($request -> id);
        $find = $config -> config_key;
        $find = 'global.'.$find;

        $dir1 = str_replace('/public', '', $_SERVER['DOCUMENT_ROOT']).'app';
        $dir2 = str_replace('/public', '', $_SERVER['DOCUMENT_ROOT']).'resources/views';
        exec('grep -rl "'.$find.'" '.$dir1, $results);
        exec('grep -rl "'.$find.'" '.$dir2, $results2);
        if(count($results) > 0 || count($results2) > 0) {
            return response([
                'status' => 'error',
                'results' => $results,
                'results2' => $results2,
            ]);
        }

        $config -> delete();

        return response() -> json(['status' => 'success']);

    }

    public function replace_keys_in_files($find, $replace) {

        $find = 'global.'.$find;
        $replace = 'global.'.$replace;

        $dir1 = str_replace('/public', '', $_SERVER['DOCUMENT_ROOT']).'app';
        $dir2 = str_replace('/public', '', $_SERVER['DOCUMENT_ROOT']).'resources/views';
        exec('grep -rl "'.$find.'" '.$dir1.' | xargs sed -i s/'.$find.'/'.$replace.'/g');
        exec('grep -rl "'.$find.'" '.$dir2.' | xargs sed -i s/'.$find.'/'.$replace.'/g');
        exec('cd '.str_replace('/public', '', $_SERVER['DOCUMENT_ROOT']).' && git add . && git commit -m "daily changes" && git push origin main');

    }
}

