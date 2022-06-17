<?php

namespace App\Http\Controllers\Notes;

use App\Models\Notes\Notes;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotesController extends Controller
{

    public function notes(Request $request) {

        return view('notes/notes');

    }

    public function get_notes(Request $request) {

        $notes = Notes::where('note_type', $request -> note_type) -> first();

        return view('notes/get_notes_html', compact('notes'));

    }


    public function save_notes(Request $request) {

        Notes::where('note_type', $request -> note_type) -> first() -> update([
            'notes' => $request -> notes
        ]);

    }

}
