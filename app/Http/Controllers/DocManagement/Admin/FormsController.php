<?php

namespace App\Http\Controllers\DocManagement\Admin;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Esign\EsignTemplates;
use Illuminate\Support\Facades\Storage;
use App\Models\DocManagement\Admin\Forms\Forms;
use thiagoalessio\TesseractOCR\TesseractOCR;
use App\Models\DocManagement\Admin\Forms\FormsPages;
use App\Models\DocManagement\Resources\FormTags;
use App\Models\DocManagement\Resources\FormGroups;
use App\Models\Esign\EsignTemplatesDocumentImages;
use App\Models\DocManagement\Resources\ChecklistGroups;

class FormsController extends Controller
{

    public function forms(Request $request) {

        $checklist_groups = ChecklistGroups::get();
        $form_groups = FormGroups::get();
        $form_tags = FormTags::get();
        $active_states = config('global.company_active_states');

        return view('/doc_management/admin/forms/forms', compact('checklist_groups', 'form_groups', 'form_tags', 'active_states'));

    }

    public function get_form_groups(Request $request) {

        $form_groups = FormGroups::with(['forms' => function($query) {
            $query -> select('id', 'form_group_id', 'checklist_group_id', 'form_name', 'form_name_display', 'form_location', 'helper_text', 'state', 'created_at')
            -> where('active', 'yes');
        }]) -> get();

        return view('/doc_management/admin/forms/get_form_groups_html', compact('form_groups'));

    }

    public function get_forms(Request $request) {

        $form_group_id = $request -> form_group_id;
        $sort_by = $request -> sort_by;
        $order = $sort_by == 'created_at' ? 'desc' : 'asc';
        $published = $request -> published;
        $active = $request -> active;

        $forms = Forms::select(['id', 'form_group_id', 'checklist_group_id', 'form_name', 'form_name_display', 'form_location', 'helper_text', 'state', 'created_at'])
        -> where('form_group_id', $form_group_id)
        -> where(function($query) use ($active) {
            if($active != '') {
                $query -> where('active', $active);
            }
        })
        -> where(function($query) use ($published) {
            if($published != '') {
                $query -> where('published', $published);
            }
        })
        -> with(['fields'])
        -> orderBy($sort_by, $order)
        -> get();

        return view('/doc_management/admin/forms/get_forms_html', compact('forms'));

    }

    public function search_forms(Request $request) {

        $value = $request -> value;
        $forms = Forms::select(['id', 'form_group_id', 'form_name_display', 'state'])
        -> where('form_name_display', 'like', '%'.$value.'%')
        -> with(['form_group', 'checklist_group'])
        -> get()
        -> toJson();

        return compact('forms');
    }

    public function get_upload_text(Request $request) {

		$upload = $request -> file('upload');

        $new_file_name = str_replace('.pdf', '', $upload -> getClientOriginalName());
        $sanitized = date('YmdHis').'_'.Helper::sanitize($new_file_name);
        $new_file_name_pdf = $sanitized.'.pdf';
        $new_file_name_image = $sanitized.'.png';

        // get first page of upload
        exec('pdftk '.$upload.' cat 1 output '.Storage::path('tmp/'.$new_file_name_pdf));
        // convert to image
        exec('convert '.$upload.' -density 200 -flatten -trim -quality 80% -background white '.Storage::path('tmp/'.$new_file_name_image));

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

        $file_title = preg_replace('/[-_]+/', ' ', $upload -> getClientOriginalName());
        $file_title = preg_replace('/\.pdf/', '', $file_title);
        $file_title = preg_replace('/[0-9\s-]+$/', '', $file_title);
        $file_title = preg_replace('/Changes\sIncluded/i', '', $file_title);
        $titles = [$file_title];
        foreach ($lines as $line) {
            // clean lines
            $line = trim(urldecode($line));
            $line = iconv('UTF-8', 'ASCII//IGNORE//TRANSLIT', $line);
            $line = preg_replace('/\.pdf/', '', $line);
            $line = preg_replace('/$[0-9\s]+/', '', $line);
            $line = preg_replace('/[0-9\s]+^/', '', $line);


            // get words
            if (preg_match('/^[a-zA-Z-_\/\s]+/', $line, $matches)) {
                // remove non form names
                if (! preg_match('/(realtor|association|commission)/i', $matches[0])) {
                    // if more than one word in name
                    preg_match_all('/\S+/', $matches[0], $words);
                    if(count($words[0]) > 1) {
                        $titles[] = ucwords(strtolower($matches[0]));
                    }
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

    public function save_form(Request $request) {

        $file = $request -> file('upload') ?? null;
        $form_id = $request -> form_id ?? null;

        if(!$form_id) {
            $validator = $request -> validate([
                'upload' => 'file|required'
            ]);
        }
        $validator = $request -> validate([
            'form_name_display' => 'required',
            'state' => 'required',
            'checklist_group_id' => 'required',
            'form_group_id' => 'required',
            'helper_text' => 'required',
        ]);

        if($file) {

            $form_name_display = $request['form_name_display'];
            $checklist_group_id = $request['checklist_group_id'];
            $form_group_id = $request['form_group_id'];
            $form_tags = $request['form_tags'];
            $state = $request['state'];
            $helper_text = $request['helper_text'];

            $page_width = Helper::get_file_info($file)['width'];
            $page_height = Helper::get_file_info($file)['height'];
            $pages_total = Helper::get_file_info($file)['pages'];
            $page_size = '';
            if ($page_width == 612 && $page_height == 792) {
                $page_size = 'letter';
            } elseif ($page_width == 595 && $page_height == 842) {
                $page_size = 'a4';
            }

            $upload_files = null;
            $template_files = null;

            $form_name = Helper::clean_file_name($file, '');

            DB::beginTransaction();

            try {

                // if existing delete all files and images
                if($form_id) {

                    $this -> clear_form_files($form_id);

                    $form = Forms::find($form_id);
                    if ($form -> published == 'no') {
                        $form -> checklist_group_id = $checklist_group_id;
                        $form -> form_group_id = $form_group_id;
                    }

                } else {

                    $form = new Forms();
                    $form -> checklist_group_id = $checklist_group_id;
                    $form -> form_group_id = $form_group_id;

                }

                $form -> form_name = $form_name;
                $form -> form_name_display = $form_name_display;
                $form -> state = $state;
                $form -> helper_text = $helper_text;
                $form -> form_tag = $form_tags;
                $form -> pages_total = $pages_total;
                $form -> page_width = $page_width;
                $form -> page_height = $page_height;
                $form -> page_size = $page_size;
                $form -> save();
                $form_id = $form -> id;

                // main storage location
                $storage_dir = 'doc_management/forms/'.$form_id;
                // create directories
                $storage_dir_pages = $storage_dir.'/pages';
                Storage::makeDirectory($storage_dir_pages);
                $storage_dir_images = $storage_dir.'/images';
                Storage::makeDirectory($storage_dir_images);

                // esign template directories
                $template_dir = 'esign_templates/system/'.$form_id;
                Storage::makeDirectory($template_dir);
                Storage::makeDirectory($template_dir.'/images');

                // save uploaded form to disk
                Storage::put($storage_dir.'/'.$form_name, file_get_contents($file));

                $file_in = Storage::path($storage_dir.'/'.$form_name);
                $file_out = Storage::path($storage_dir.'/temp_'.$form_name);
                exec('pdftk '.$file_in.' output '.$file_out.' flatten compress');
                exec('rm '.$file_in.' && mv '.$file_out.' '.$file_in);

                // copy file to esign templates
                Storage::copy($storage_dir.'/'.$form_name, $template_dir.'/'.$form_name);


                // add to esign_templates
                if($request -> form_id) {
                    $template = EsignTemplates::where('system_form_id', $form_id) -> first();
                } else {
                    $template = new EsignTemplates();
                    $template -> system_form_id = $form_id;
                }
                $template -> template_type = 'system';
                $template -> template_name = $form_name_display;
                $template -> file_name = $form_name;
                $template -> file_location = $template_dir.'/'.$form_name;
                $template -> save();
                $template_id = $template -> id;

                // update form with template id
                $form -> esign_template_id = $template_id;
                // update form directory path in database
                $form -> form_location = $storage_dir.'/'.$form_name;
                $form -> save();


                // split pdf into pages and images
                $input_file = Storage::path($storage_dir.'/'.$form_name);
                $output_files = Storage::path($storage_dir_pages.'/%d.pdf');
                $output_images = Storage::path($storage_dir_images.'/%d.jpg');

                // add individual pages to pages directory
                $create_pages = exec('pdftk '.$input_file.' burst output '.$output_files.' flatten compress');

                // remove data file
                Storage::delete($storage_dir_pages.'/doc_data.txt');

                $split_pages_directory = Storage::files($storage_dir_pages);

                foreach ($split_pages_directory as $split_page) {

                    $c = str_replace('.pdf', '', basename($split_page));
                    // add individual images to images directory
                    $create_image = exec('convert -density 200 -quality 80% '.Storage::path($split_page).' -compress JPEG -background white -alpha remove -strip '.Storage::path($storage_dir_images.'/'.$c.'.jpg')); // removed  -resize 1200

                    // add pages to db
                    $upload_pages = new FormsPages();
                    $upload_pages -> form_id = $form_id;
                    $upload_pages -> pdf_location = $storage_dir_pages.'/'.$c.'.pdf';
                    $upload_pages -> image_location = $storage_dir_images.'/'.$c.'.jpg';
                    $upload_pages -> pages_total = $pages_total;
                    $upload_pages -> page_number = $c;
                    $upload_pages -> save();

                    $template_image_location = $template_dir.'/images/'.$c.'.jpg';
                    // copy image to templates directory
                    Storage::copy($storage_dir_images.'/'.$c.'.jpg', $template_dir.'/images/'.$c.'.jpg');

                    $page_width = Helper::get_file_info(Storage::path($split_page))['width'];
                    $page_height = Helper::get_file_info(Storage::path($split_page))['height'];

                    // add to template images
                    $template_image = new EsignTemplatesDocumentImages();
                    $template_image -> template_id = $template_id;
                    $template_image -> file_location = $template_dir.'/images/'.$c.'.jpg';
                    $template_image -> page_number = $c;
                    $template_image -> width = $page_width;
                    $template_image -> height = $page_height;
                    $template_image -> save();

                    $c += 1;

                }

                DB::commit();

            } catch (\Exception $e) {

                DB::rollback();
                // only delete directories if a new upload
                if(!$request -> form_id) {
                    $this -> clear_form_files($form_id);
                }

                return response() -> json(['error' => $e -> getMessage()]);

            }

        } else {

            // if file not being replaced
            $form_id = $request -> form_id;
            $form_name_display = $request -> form_name_display;
            $form_group_id = $request -> form_group_id;
            $checklist_group_id = $request -> checklist_group_id;
            $helper_text = $request -> helper_text;
            $form_tag = $request -> form_tag;

            $form = Forms::where('id', $form_id) -> first();

            if ($form -> published == 'no') {
                $form -> form_group_id = $form_group_id;
                $form -> checklist_group_id = $checklist_group_id;
            }

            $form -> form_name_display = $form_name_display;
            $form -> helper_text = $helper_text;
            $form -> form_tag = $form_tag;
            $form -> save();

            return response() -> json(['success' => true]);

        }

    }

    public function clear_form_files($form_id) {

        Storage::deleteDirectory('doc_management/forms/'.$form_id);
        Storage::deleteDirectory('esign_templates/system/'.$form_id);

        $delete_upload_images = FormsPages::where('form_id', $form_id) -> delete();
        $template = EsignTemplates::where('system_form_id', $form_id) -> first();
        if($template) {
            $delete_template_images = EsignTemplatesDocumentImages::where('template_id', $template -> id) -> delete();
        }

    }

}
