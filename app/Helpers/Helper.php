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
        $clean = $anal ? preg_replace('/[^a-zA-Z0-9]/', '', $clean) : $clean;

        return $force_lowercase ?
        function_exists('mb_strtolower') ?
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

    public static function directory($directory) {
        $results = [];
        $handler = opendir($directory);

        while ($file = readdir($handler)) {
            if ($file != '.' && $file != '..') {
                $results[] = $file;
            }
        }

        closedir($handler);

        return $results;
    }

    public static function is_dir_empty($dir) {
        if (! is_readable($dir)) {
            return null;
        }

        return count(scandir($dir)) == 2;
    }

    public static function shorten_text($text, $length) {
        if (strlen($text) > $length) {
            return substr($text, 0, $length).'...';
        }

        return $text;
    }

    public static function format_phone($phone) {
        $phone = preg_replace('/[\s\(\)-]+/', '', $phone);

        return '('.substr($phone, 0, 3).') '.substr($phone, 3, 3).'-'.substr($phone, 6);
    }

    public static function date_mdy($date) {
        return date('n/j/Y', strtotime($date));
    }

    public static function get_mb($size) {
        return sprintf('%4.2f', $size / 1048576);
    }

    public static function get_initials($string) {
        if (strlen($string) > 0) {
            $words = explode(' ', $string);
            $initials = '';

            foreach ($words as $w) {
                $initials .= $w[0];
            }

            return $initials;
        }

        return '';
    }

}
