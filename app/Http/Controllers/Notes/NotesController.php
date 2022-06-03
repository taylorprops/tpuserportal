<?php

namespace App\Http\Controllers\Notes;

use App\Models\Notes\Notes;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotesController extends Controller
{

    public function notes(Request $request) {

        $notes = Notes::first();
        return view('notes/notes', compact('notes'));

    }

    public function save_notes(Request $request) {

        Notes::first() -> update([
            'notes' => $request -> notes
        ]);

    }

}
