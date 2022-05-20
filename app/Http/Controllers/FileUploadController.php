<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class FileUploadController extends Controller {
    public function file_upload(Request $request) {

        /***************************************************
         * Only these origins are allowed to upload images *
         ***************************************************/
        $accepted_origins = [config('app.url')];

        /*********************************************
         * Change this line to set the upload folder *
         *********************************************/
        $imageFolder = Storage::path('file_upload/tinymce/');

        if (isset($_SERVER['HTTP_ORIGIN'])) {

// same-origin requests won't set an origin. If the origin is set, it must be valid.
            if (in_array($_SERVER['HTTP_ORIGIN'], $accepted_origins)) {
                header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
            } else {
                header('HTTP/1.1 403 Origin Denied');

                return;
            }

        }

// Don't attempt to process the upload on an OPTIONS request
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            header('Access-Control-Allow-Methods: POST, OPTIONS');

            return;
        }

        reset($_FILES);
        $temp = current($_FILES);
        if (is_uploaded_file($temp['tmp_name'])) {

/*
If your script needs to receive cookies, set images_upload_credentials : true in
the configuration and enable the following two headers.
 */

// header('Access-Control-Allow-Credentials: true');

// header('P3P: CP="There is no P3P policy."');

// Sanitize input
            if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $temp['name'])) {
                header('HTTP/1.1 400 Invalid file name.');

                return;
            }

// Verify extension
            if (!in_array(strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION)), ['gif', 'jpg', 'jpeg', 'JPG', 'png'])) {
                header('HTTP/1.1 400 Invalid extension.');

                return;
            }

            $file = $request -> file('file');
            $file_name = $file -> getClientOriginalName();
            $ext = $file -> extension();
            $file_name = preg_replace('/\.' . $ext . '/i', '', $file_name);
            $file_name = time() . '_' . Helper::sanitize($file_name) . '.' . $ext;

            // Accept upload if there was no origin, or if it is an accepted origin
            $new_file_location = $imageFolder . $file_name;
            move_uploaded_file($temp['tmp_name'], $new_file_location);

            $img = Image::make($new_file_location);
            $width = $img -> width();
            $height = $img -> height();

            if ($img -> width() > 700) {
                $width = 700;
            }

            if ($img -> height() > 700) {
                $height = 700;
            }

            $img -> resize($width, $height, function ($constraint) {
                $constraint -> aspectRatio();
            });
            $img -> save($new_file_location);

            // Determine the base URL
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';
            $baseurl = $protocol . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['REQUEST_URI']), '/') . '/';

            // Respond to the successful upload with JSON.

            // Use a location key to specify the path to the saved image resource.
            // { location : '/your/uploaded/image/file'}

            return response() -> json(['location' => config('app.url') . '/storage/file_upload/tinymce/' . $file_name]);
        } else {
            // Notify editor that the upload failed
            header('HTTP/1.1 500 Server Error');
        }

    }

}