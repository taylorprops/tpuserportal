<?php

namespace App\Http\Controllers\DocManagement\Admin;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use thiagoalessio\TesseractOCR\TesseractOCR;

class FormsController extends Controller
{

    public function forms(Request $request) {

        return view('/doc_management/admin/forms');

    }

    public function get_upload_text(Request $request) {

		$upload = $request -> file('upload');

        $new_file_name = str_replace('.pdf', '', $upload -> getClientOriginalName());
        $sanitized = date('YmdHis').'_'.Helper::sanitize($new_file_name);
        $new_file_name_pdf = $sanitized.'.pdf';
        $new_file_name_image = $sanitized.'.png';

        // put original file
        Storage::put('tmp/'.$new_file_name_pdf, file_get_contents($upload));

        // remove images from pdf so easier to scan text
        exec('gs -o '.Storage::path('tmp/tmp_'.$new_file_name_pdf).' -sDEVICE=pdfwrite -dFILTERIMAGE '.$upload);
        // convert to image
        exec('convert '.Storage::path('tmp/tmp_'.$new_file_name_pdf).'[0] -density 200 -flatten -trim -quality 80% -background white '.Storage::path('tmp/'.$new_file_name_image));
        // scan text
        $text = (new TesseractOCR(Storage::path('tmp/'.$new_file_name_image)))
            -> allowlist(range('a', 'z'), range('A', 'Z'), '-_/\'/')
            -> run();
        // store results to text file so we can loop through the lines
        $temp_text_file = 'tmp/'.date('YmdHis').'.txt';
        Storage::put($temp_text_file, $text);
        // open saved text file with titles and get lines
        $fn = fopen(Storage::path($temp_text_file), 'r');
        $lines = [];
        while (! feof($fn)) {
            $lines[] = fgets($fn);
        }
        fclose($fn);

        $file_title = preg_replace('/_/', ' ', $upload -> getClientOriginalName());
        $file_title = preg_replace('/\.pdf/', '', $file_title);
        $titles = [$file_title];
        foreach ($lines as $line) {
            // clean lines
            $line = trim(urldecode($line));
            $line = iconv('UTF-8', 'ASCII//IGNORE//TRANSLIT', $line);
            $line = preg_replace('/\.pdf/', '', $line);


            // get words
            if (preg_match('/^[a-zA-Z-_\/\s]+/', $line, $matches)) {
                // remove non form names
                if (! preg_match('/(realtor|association|commission)/i', $matches[0])) {
                    // if more than one word in name
                    preg_match_all('/\S+/', $matches[0], $words);
                    //if(count($words[0]) > 1) {
                    $titles[] = ucwords(strtolower($matches[0]));
                    //}
                }
            }
        }

        $titles = array_slice($titles, 0, 5);

        $upload_location = '/storage/tmp/'.$new_file_name_pdf;

        return response() -> json([
            'upload_location' => $upload_location,
            'titles' => $titles,
        ]);
    }

}
