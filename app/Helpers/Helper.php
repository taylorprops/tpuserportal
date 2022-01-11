<?php

namespace App\Helpers;

use App\Models\User;
use ReflectionClass;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Helper
{

    public static function clean_file_name($file, $new_ext, $remove_numbers = false, $add_time = false) {

        $file_name = $file -> getClientOriginalName();
        $file_ext = $file -> getClientOriginalExtension();

        $file_name = str_replace('.'.$file_ext, '', $file_name);
        if($remove_numbers == true) {
            $file_name = preg_replace('/[0-9]+/', '', $file_name);
        }
        $file_name = Helper::sanitize($file_name);

        if($add_time == true) {
            $file_name .= '_'.time();
        }

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

    public static function to_excel($rows, $filename, $select) {

        $spreadsheet = new Spreadsheet();
        $Excel_writer = new Xlsx($spreadsheet);

        $spreadsheet -> setActiveSheetIndex(0);
        $activeSheet = $spreadsheet -> getActiveSheet();

        for ($i = 0, $char = 'A'; $i < count($select); $i++, $char++) {
            $activeSheet -> setCellValue($char.'1', ucwords(str_replace('_', ' ', $select[$i])));
        }

        $count = 2;
        foreach ($rows as $row) {
            $char = 'A';
            foreach ($row as $key => $value) {
                $activeSheet -> setCellValue($char.$count, $value);
                $char++;
            }
            $count++;
        }

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename='. $filename);
        header('Cache-Control: max-age=0');
        $file_location = Storage::path('tmp/'.$filename);
        $url = Storage::url('tmp/'.$filename);
        $Excel_writer -> save($file_location);

        return $url;

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

    public static function access_protected_property($object, $key) {

        $reflection = new ReflectionClass($object);
        $property = $reflection -> getProperty($key);
        $property -> setAccessible(true);
        return $property -> getValue($object);

    }

    public static function rets_login() {

        date_default_timezone_set('America/New_York');
        $rets_config = new \PHRETS\Configuration;
        $rets_config -> setLoginUrl(config('global.rets_url'))
        -> setUsername(config('global.rets_username'))
        -> setPassword(config('global.rets_password'))
        -> setRetsVersion('RETS/1.7.2')
        -> setUserAgent('Bright RETS Application/1.0')
        -> setHttpAuthenticationMethod('digest')
        -> setOption('use_post_method', true)
        -> setOption('disable_follow_location', false);

        $rets = new \PHRETS\Session($rets_config);

        $connect = $rets -> Login();

        return $rets;
    }

    public static function parse_address_google($address) {

        $url = 'https://maps.googleapis.com/maps/api/geocode/json?sensor=false&address=' . urlencode($address).'&key='.config('global.google_api_key');
        $results = json_decode(file_get_contents($url), 1);

        $parts = array(
            'street_number' => array('street_number'),
            'address' => array('street_number', 'route'),
            'unit' => array('subpremise'),
            'city' => array('locality'),
            'state' => array('administrative_area_level_1'),
            'zip' => array('postal_code'),
        );

        if (!empty($results['results'][0]['address_components'])) {
            $ac = $results['results'][0]['address_components'];
            dump($ac);
            foreach ($parts as $need => &$types) {

                foreach ($ac as &$a) {

                    if (in_array($a['types'][0], $types)) {
                        $address_out[$need] = $a['short_name'];
                    } elseif (empty($address_out[$need])) {
                        $address_out[$need] = '';
                    }

                }

            }

        } else {
            return 'error';
        }

        return $address_out;

    }

    public static function avatar($user_id = null, $user_type = null) {

        $bg_color = 'bg-blue-400';
        if($user_id && $user_id != auth() -> user() -> user_id) {
            $user = User::where('user_id', $user_id) -> where('group', $user_type) -> first();
        } else {
            $user = User::find(auth() -> user() -> id);
            $bg_color = 'bg-green-400';
        }

        /* $colors = [
            'bg-blue-700',
            'bg-red-700',
            'bg-emerald-700',
            'bg-orange-700',
            'bg-green-700',
            'bg-teal-700',
            'bg-cyan-700',
            'bg-indigo-700',
            'bg-purple-700',
        ];

        $selected_color = $colors[0];

        $cont = true;
        if(session() -> has('avatar_colors')) {
            foreach($colors as $color) {
                if($cont == true) {
                    if(!in_array($color, session() -> get('avatar_colors'))) {
                        $selected_color = $color;
                        session() -> push('avatar_colors', $colors[0]);
                        $cont = false;
                    }
                }
            }
        } else {
            session(['avatar_colors' => [$colors[0]]]);
        } */

        if($user && $user -> photo_location_url) {
            return '<div class="bg-cover bg-bottom rounded-full w-10 h-10" style="background-image: url('.$user -> photo_location_url.')"></div>';
        } else {
            return '<div class="rounded-full '.$bg_color.' text-white text-lg flex items-center justify-around h-10 w-10">
                '.Helper::get_initials($user -> first_name.' '.$user -> last_name).'
            </div>';
        }
    }
}
