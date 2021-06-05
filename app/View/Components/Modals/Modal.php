<?php

namespace App\View\Components\Modals;

use Illuminate\View\Component;

class Modal extends Component
{


    public $modalWidth;
    public $modalTitle;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($modalWidth, $modalTitle)
    {
        $this -> modalWidth = $modalWidth;
        $this -> modalTitle = $modalTitle;
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
