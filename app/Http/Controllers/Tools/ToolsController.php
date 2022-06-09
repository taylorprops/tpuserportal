<?php

namespace App\Http\Controllers\Tools;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ToolsController extends Controller
{

    public function tools(Request $request) {

        return view('tools/tools');

    }

    public function create_classes(Request $request) {

        $type = $request -> type;
        $style = $request -> style;
        $level = $request -> level;
        $single = $request -> single;

        $classes = '';
        $classes_response = '';

        if($type == 'multiple') {

            $colors = ['red', 'orange', 'amber', 'yellow', 'lime', 'green', 'emerald', 'teal', 'cyan', 'sky', 'blue', 'indigo', 'violet', 'purple', 'fuchsia', 'rose'];

            foreach($colors as $color) {

                $classes .= $style.'-'.$color.'-'.$level."\n";
                $classes_response .= $style.'-'.$color.'-'.$level.'<br>';

            }

        }

        if($type == 'single') {

            $classes = $single."\n";
            $classes_response = $single;

        }

        $classes .= "\n";

        $file = Storage::append('/tools/safelist.txt', $classes);

        return $classes_response;


    }
}
