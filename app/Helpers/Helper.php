<?php

namespace App\Helpers;

class Helper
{

    public static function clean_file_name($file, $new_ext, $remove_numbers = false) {

        $file_name = $file -> getClientOriginalName();
        $file_ext = $file -> getClientOriginalExtension();

        $file_name = str_replace('.'.$file_ext, '', $file_name);
        if($remove_numbers == true) {
            $file_name = preg_replace('/[0-9]+/', '', $file_name);
        }
        $file_name = Helper::sanitize($file_name);

        if($new_ext != '') {
            return $file_name.'.'.$new_ext;
        }

        return $file_name.'.'.$file_ext;

    }

    public static function sanitize($string, $force_lowercase = false, $anal = false) {

        $strip = ['~', '`', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '=', '+', '[', '{', ']', '}', '\\', '|', ';', ':', '"', "'", '&#8216;', '&#8217;', '&#8220;', '&#8221;', '&#8211;', '&#8212;', 'â€”', 'â€“', ',', '<', '.', '>', '/', '?'];
        $clean = trim(str_replace($strip, '', strip_tags($string)));
        $clean = preg_replace('/\s+/', '-', $clean);
        $clean = ($anal) ? preg_replace('/[^a-zA-Z0-9]/', '', $clean) : $clean;

        return ($force_lowercase) ?
        (function_exists('mb_strtolower')) ?
        mb_strtolower($clean, 'UTF-8') :
        strtolower($clean) :
        $clean;

    }

    public static function get_file_info($file) {

        exec('identify '.$file, $output, $return);

        $width = '';
        $height = '';
        $pages = count($output);
        if ($output) {
            preg_match('/[0-9]+x[0-9]+/', $output[0], $match);
            $size = $match[0];
            $width = substr($size, 0, strpos($size, 'x'));
            $height = substr($size, strpos($size, 'x') + 1);
        }

        return ['width' => $width, 'height' => $height, 'pages' => $pages];

    }

}
