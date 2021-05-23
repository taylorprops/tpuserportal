<?php

namespace App\View\Components\Modals;

use Illuminate\View\Component;

class Modal extends Component
{


    public $dispatchId;
    public $width;
    public $title;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($dispatchId, $width, $title)
    {
        $this -> dispatchId = $dispatchId;
        $this -> width = $width;
        $this -> title = $title;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.modals.modal');
    }
}
